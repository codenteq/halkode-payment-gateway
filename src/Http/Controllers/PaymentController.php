<?php

namespace Webkul\Halkode\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Webkul\Checkout\Facades\Cart;
use Webkul\Halkode\Payment\Halkode;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Transformers\OrderResource;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository,
        protected Halkode $halkode
    ) {
        //
    }

    /**
     * Redirects to the Halk Öde payment page.
     */
    public function redirect()
    {
        $cart = Cart::getCart();

        return view('halkode::pay-smart-3d', [
            'cart'       => $cart,
            'invoice_id' => $cart->id,
            'total'      => number_format($cart->grand_total, 2, '.', ''),
        ]);
    }

    /**
     * Redirects to the 3D.
     */
    public function callback(Request $request)
    {
        $cart = Cart::getCart();
        $invoiceId = uniqid('HALKODE_');

        $items = $cart->items->map(fn($p) => [
            'name'        => $p->name,
            'price'       => round($p->total_incl_tax / $p->quantity, 2),
            'quantity'    => $p->quantity,
            'description' => $p->getTypeInstance()->isStockable() ? 'PHYSICAL_GOODS' : 'DIGITAL_GOODS',
        ])->toArray();

        if ($cart->shipping_amount_incl_tax > 0) {
            $items[] = [
                'name'        => 'Shipping',
                'price'       => round($cart->shipping_amount_incl_tax, 2),
                'quantity'    => 1,
                'description' => 'SERVICE',
            ];
        }

        if ($diff = round($cart->grand_total - collect($items)->sum(fn($i) => $i['price'] * $i['quantity']), 2)) {
            $items[array_key_last($items)]['price'] = round($items[array_key_last($items)]['price'] + $diff, 2);
        }

        $payload = [
            "cc_holder_name"      => $request->cc_holder_name,
            "cc_no"               => $request->cc_no,
            "expiry_month"        => $request->expiry_month,
            "expiry_year"         => $request->expiry_year,
            "cvv"                 => $request->cvv,
            "currency_code"       => "TRY",
            "installments_number" => $request->installments_number,
            "invoice_id"          => $invoiceId,
            "invoice_description" => "Grand total:" . $cart->grand_total,
            "total"               => number_format($cart->grand_total, 2, '.', ''),
            "items"               => json_encode($items),
            "name"                => $cart['customer_first_name'],
            "surname"             => $cart['customer_last_name'],
            "merchant_key"        => $this->halkode->getMerchantKey(),
            "hash_key"            => $this->generateHash(number_format($cart->grand_total, 2, '.', ''), $request->installments_number, 'TRY', $this->halkode->getMerchantKey(), $invoiceId, $this->halkode->getAppSecret()),
            "return_url"          => route('halkode.success'),
            "cancel_url"          => route('halkode.cancel'),
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])
            ->timeout(30)
            ->post($this->halkode->getPaymentUrl() . '/api/paySmart3D', $payload);

        if ($response->failed()) {
            return redirect()->route('halkode.cancel');
        }

        return $response->body();
    }

    /**
     * Place an order and redirect to the success page.
     */
    public function success(Request $request)
    {
        logger()->info(['Halk Öde payment successful.' => $request->query()]);

        $cart = Cart::getCart();

        $data = (new OrderResource($cart))->jsonSerialize();

        $order = $this->orderRepository->create($data);

        if ($order->canInvoice()) {
            $this->invoiceRepository->create($this->prepareInvoiceData($order));
        }

        Cart::deActivateCart();

        session()->flash('order_id', $order->id);

        return redirect()->route('shop.checkout.onepage.success');
    }

    /**
     * Redirect to the cart page with error message.
     */
    public function failure(Request $request)
    {
        logger()->error(['Halk Öde payment failed or was cancelled.' => $request->query()]);

        session()->flash('error', $request->query()['error']);

        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Prepares order's invoice data for creation.
     */
    protected function prepareInvoiceData($order): array
    {
        $invoiceData = [
            'order_id' => $order->id,
            'invoice'  => ['items' => []],
        ];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }

    protected function generateHash($total, $installment, $currency_code, $merchant_key, $invoice_id, $app_secret)
    {
        $data = $total . '|' . $installment . '|' . $currency_code . '|' . $merchant_key . '|' . $invoice_id;
        $iv = substr(sha1(mt_rand()), 0, 16);
        $password = sha1($app_secret);
        $salt = substr(sha1(mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password . $salt);
        $encrypted = openssl_encrypt("$data", 'aes-256-cbc', "$saltWithPassword", 0, $iv);
        $msg_encrypted_bundle = "$iv:$salt:$encrypted";
        $msg_encrypted_bundle = str_replace('/', '__', $msg_encrypted_bundle);
        return $msg_encrypted_bundle;
    }
}

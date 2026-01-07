@push('meta')
    <meta name="description" content="@lang('halkode::app.halkode.info')"/>
    <meta name="keywords" content="@lang('halkode::app.halkode.info')"/>
@endPush

@push('scripts')
    <script>
        const installmentMap = {};

        async function updateInstallments() {
            const ccNo = document.getElementById('cc_no').value;
            const total = document.getElementById('total')?.value || "{{ $total }}";

            if (ccNo.length !== 16 || !total) return;

            try {
                const res = await fetch("{{ route('halkode.installments') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({credit_card: ccNo, amount: total})
                });

                const {data} = await res.json();
                if (!Array.isArray(data)) return;

                const select = document.getElementById('installments_number');

                for (const key in installmentMap) delete installmentMap[key];

                select.innerHTML = data.map(({installments_number: num, amount_to_be_paid: amt, currency_code: cur}) => {
                    const total = parseFloat(amt);
                    installmentMap[num] = total;

                    const text = num === 1
                        ? `Tek Çekim - ${total.toFixed(2)} ${cur}`
                        : `${num} Taksit - Aylık: ${(total / num).toFixed(2)} ${cur}`;

                    return `<option value="${num}">${text}</option>`;
                }).join('');

                updatePayButton();
            } catch (err) {
                console.error('Hata:', err);
                alert('Bir hata oluştu.');
            }
        }

        function updatePayButton() {
            const num = document.getElementById('installments_number').value;
            const total = installmentMap[num];
            const btn = document.getElementById('pay-button');

            if (total && btn) {
                btn.innerText = `@lang('halkode::app.resources.actions.pay') ${total.toFixed(2)} {{ core()->getCurrentCurrencyCode() }}`;
            }
        }

        document.addEventListener('change', e => {
            if (e.target.id === 'installments_number') updatePayButton();
        });
    </script>
@endpush

<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <x-slot:title>
        @lang('halkode::app.resources.title')
    </x-slot>

    <div class="min-h-screen w-[500px] mx-auto p-4">
        <div class="max-w-[500px] rounded-xl shadow-xl p-6 sm:p-8 shadow-[0_5px_20px_rgba(0,0,0,0.15)]">
            <div class="flex items-center justify-between mb-8">
                <div class="group flex items-center gap-2 text-sm font-medium text-zinc-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="h-4 w-4 transition-transform group-hover:-translate-x-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                    </svg>

                    <a href="{{ route('shop.checkout.cart.index') }}" class="font-medium hover:underline">
                        @lang('halkode::app.resources.actions.back_to_cart')
                    </a>
                </div>

                <div>
                    @if ($logo = core()->getCurrentChannel()->logo_url)
                        <img
                            src="{{ $logo }}"
                            alt="{{ config('app.name') }}"
                            style="height: 40px; width: 110px;"
                        />
                    @else
                        <img
                            src="{{ bagisto_asset('images/logo.svg', 'shop') }}"
                            alt="{{ config('app.name') }}"
                            width="131"
                            height="29"
                            style="width: 156px;height: 40px;"
                        />
                    @endif
                </div>
            </div>

            <x-shop::form
                method="POST"
                :action="route('halkode.callback')"
                class="space-y-4"
            >
                @csrf

                <div class="mb-5">
                    <x-shop::form.control-group.control
                        type="text"
                        class="block w-[420px] max-w-full rounded-xl border-2 px-5 py-4 text-base max-1060:w-full max-md:p-3.5 max-sm:rounded-lg max-sm:border-2 max-sm:p-2 max-sm:text-sm"
                        name="cc_holder_name"
                        rules="required"
                        label="{{ __('halkode::app.resources.form.card_holder_name') }}"
                        placeholder="{{ __('halkode::app.resources.form.card_holder_name_placeholder') }}"
                    />

                    <x-shop::form.control-group.error control-name="cc_holder_name"/>
                </div>

                <div class="mb-5">
                    <x-shop::form.control-group.control
                        id="cc_no"
                        type="text"
                        class="block w-[420px] max-w-full rounded-xl border-2 px-5 py-4 text-base max-1060:w-full max-md:p-3.5 max-sm:mb-5 max-sm:rounded-lg max-sm:border-2 max-sm:p-2 max-sm:text-sm"
                        name="cc_no"
                        rules="required"
                        oninput="if(this.value.length >= 6) updateInstallments()"
                        label="{{ __('halkode::app.resources.form.card_number') }}"
                        placeholder="{{ __('halkode::app.resources.form.card_number') }}"
                        maxlength="16"
                    />

                    <x-shop::form.control-group.error control-name="cc_no"/>
                </div>

                <div class="grid grid-cols-12 gap-4 mb-5">
                    <div class="flex gap-2">
                        <div>
                            <x-shop::form.control-group.control
                                type="text"
                                class="block w-[420px] max-w-full rounded-xl border-2 px-5 py-4 text-base max-1060:w-full max-md:p-3.5 max-sm:mb-5 max-sm:rounded-lg max-sm:border-2 max-sm:p-2 max-sm:text-sm"
                                name="expiry_month"
                                rules="required"
                                label="{{ __('halkode::app.resources.form.expiry_month') }}"
                                placeholder="{{ __('halkode::app.resources.form.expiry_month_placeholder') }}"
                                maxlength="2"
                                inputmode="numeric"
                            />

                            <x-shop::form.control-group.error control-name="expiry_month"/>
                        </div>

                        <div>
                            <x-shop::form.control-group.control
                                type="text"
                                class="block w-[420px] max-w-full rounded-xl border-2 px-5 py-4 text-base max-1060:w-full max-md:p-3.5 max-sm:mb-5 max-sm:rounded-lg max-sm:border-2 max-sm:p-2 max-sm:text-sm"
                                name="expiry_year"
                                rules="required"
                                label="{{ __('halkode::app.resources.form.expiry_year') }}"
                                placeholder="{{ __('halkode::app.resources.form.expiry_year_placeholder') }}"
                                maxlength="2"
                                inputmode="numeric"
                            />

                            <x-shop::form.control-group.error control-name="expiry_year"/>
                        </div>

                        <div>
                            <x-shop::form.control-group.control
                                type="password"
                                class="block w-[420px] max-w-full rounded-xl border-2 px-5 py-4 text-base max-1060:w-full max-md:p-3.5 max-sm:mb-5 max-sm:rounded-lg max-sm:border-2 max-sm:p-2 max-sm:text-sm"
                                name="cvv"
                                rules="required"
                                label="{{ __('halkode::app.resources.form.cvv') }}"
                                placeholder="{{ __('halkode::app.resources.form.cvv') }}"
                                maxlength="3"
                                inputmode="numeric"

                            />

                            <x-shop::form.control-group.error control-name="cvv"/>
                        </div>
                    </div>

                    <div>
                        <select
                            id="installments_number"
                            name="installments_number"
                            required
                            onchange="updatePayButton()"
                            class="custom-select mb-1.5 w-full rounded-lg border border-zinc-200 bg-white px-5 py-3 text-base text-gray-600 transition-all hover:border-gray-400 focus-visible:outline-none max-md:py-2 max-sm:px-4 max-sm:text-sm"
                        >
                            <option value="1" selected>
                                @lang('halkode::app.resources.form.single_payment')
                            </option>
                        </select>
                    </div>
                </div>

                <x-shop::button
                    id="pay-button"
                    type="submit"
                    class="secondary-button w-full max-w-full max-md:py-3 max-sm:rounded-lg max-sm:py-1.5"
                    :title="__('halkode::app.resources.actions.pay') . ' ' . number_format($total, 2) . ' ' . core()->getCurrentCurrencyCode()"
                />
            </x-shop::form>

            <div class="mt-6 flex items-center justify-center gap-2 text-xs text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-3">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                </svg>

                <span>@lang('halkode::app.resources.security.ssl')</span>
            </div>

            <div class="mt-6">
                <img
                    src="{{ asset('vendor/halkode/images/logo_band_colored.svg') }}"
                    alt="Halkode"
                    style="height: 80px; width: 220px; margin: 0 auto;"
                />
            </div>
        </div>
    </div>
</x-shop::layouts>

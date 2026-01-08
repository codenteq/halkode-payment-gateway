<?php

return [
    'halkode' => [
        'info'              => 'Halkbank ödeme yöntemi güvenli ve hızlı ödeme seçeneği.',
        'name'              => 'Halk Öde',
        'payment'           => 'Halkbank Ödeme Ağ Geçidi',
        'title'             => 'Banka veya Kredi Kartı',
        'description'       => 'Halk Öde',

        'system' => [
            'title'                => 'Başlık',
            'description'          => 'Açıklama',
            'image'                => 'Logo',
            'status'               => 'Durum',
            'merchant_key'         => 'Üye İşyeri Anahtarı',
            'merchant_key_info'    => 'HalkÖde panosundan aldığınız Üye İşyeri Anahtarını girin',
            'app_secret'           => 'Uygulama Parolası',
            'app_secret_info'      => 'HalkÖde panosundan aldığınız Uygulama Parolasını girin',
            'application_key'      => 'Uygulama Anahtarı',
            'application_key_info' => 'HalkÖde panosundan aldığınız Uygulama Anahtarını girin',
        ],
    ],

    'resources' => [
        'title'             => 'Ödeme',

        'security' => [
            'ssl'           => '256-bit SSL Güvenli Ödeme',
        ],

        'actions' => [
            'back_to_cart'  => 'Sepete geri dön',
            'pay'           => 'Öde',
        ],

        'form' => [
            'card_holder_name'                  => 'Ad Soyad',
            'card_holder_name_placeholder'      => 'Kart Sahibi Ad Soyad',
            'card_number'                       => 'Kart Numarası',
            'expiry_month'                      => 'Ay',
            'expiry_month_placeholder'          => 'AA',
            'expiry_year'                       => 'Yıl',
            'expiry_year_placeholder'           => 'YY',
            'cvv'                               => 'CVV',
            'installment'                       => 'Taksit',
            'single_payment'                    => 'Tek Çekim / Taksit Seçiniz',
        ],
    ],
];

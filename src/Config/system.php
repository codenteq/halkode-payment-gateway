<?php

return [
    [
        'key'    => 'sales.payment_methods.halkode',
        'info'   => 'halkode::app.halkode.info',
        'name'   => 'halkode::app.halkode.name',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'active',
                'title'         => 'halkode::app.halkode.system.status',
                'type'          => 'boolean',
                'channel_based' => true,
                'locale_based'  => false,
            ], [
                'name'          => 'title',
                'title'         => 'halkode::app.halkode.system.title',
                'type'          => 'text',
                'depends'       => 'active:1',
                'validation'    => 'required_if:active,1',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'description',
                'title'         => 'halkode::app.halkode.system.description',
                'type'          => 'textarea',
                'depends'       => 'active:1',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'image',
                'title'         => 'halkode::app.halkode.system.image',
                'type'          => 'file',
                'info'          => 'admin::app.configuration.index.sales.payment-methods.logo-information',
                'depends'       => 'active:1',
                'channel_based' => false,
                'locale_based'  => true,
                'validation'    => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'          => 'merchant_key',
                'title'         => 'halkode::app.halkode.system.merchant_key',
                'info'          => 'halkode::app.halkode.system.merchant_key_info',
                'type'          => 'text',
                'depends'       => 'active:1',
                'channel_based' => true,
                'locale_based'  => false,
            ], [
                'name'          => 'app_secret',
                'title'         => 'halkode::app.halkode.system.app_secret',
                'info'          => 'halkode::app.halkode.system.app_secret_info',
                'type'          => 'password',
                'depends'       => 'active:1',
                'channel_based' => true,
                'locale_based'  => false,
            ], [
                'name'          => 'application_key',
                'title'         => 'halkode::app.halkode.system.application_key',
                'info'          => 'halkode::app.halkode.system.application_key_info',
                'type'          => 'password',
                'depends'       => 'active:1',
                'channel_based' => true,
                'locale_based'  => false,
            ], [
                'name'          => 'sandbox',
                'title'         => 'Sandbox',
                'type'          => 'boolean',
                'depends'       => 'active:1',
                'channel_based' => true,
                'locale_based'  => false,
            ], [
                'name'    => 'sort',
                'title'   => 'admin::app.configuration.index.sales.payment-methods.sort-order',
                'type'    => 'select',
                'depends' => 'active:1',
                'options' => [
                    [
                        'title' => '1',
                        'value' => 1,
                    ], [
                        'title' => '2',
                        'value' => 2,
                    ], [
                        'title' => '3',
                        'value' => 3,
                    ], [
                        'title' => '4',
                        'value' => 4,
                    ], [
                        'title' => '5',
                        'value' => 5,
                    ], [
                        'title' => '6',
                        'value' => 6,
                    ], [
                        'title' => '7',
                        'value' => 7,
                    ],
                ],
                'channel_based' => true,
                'locale_based'  => false,
            ],
        ],
    ],
];

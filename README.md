<p align="center"><a href="https://codenteq.com" target="_blank"><img src="src/Resources/assets/images/halkode.svg" width="288"></a></p>

# Halk Öde Payment Gateway
[![License](https://poser.pugx.org/codenteq/halkode-payment-gateway/license)](https://github.com/codenteq/halkode-payment-gateway/blob/master/LICENSE)
[![Total Downloads](https://poser.pugx.org/codenteq/halkode-payment-gateway/d/total)](https://packagist.org/packages/codenteq/halkode-payment-gateway)

## 1. Introduction:

Install this package now to receive secure payments in your online store. Halk Öde offers an easy and secure payment gateway.

## 2. Requirements:

* **PHP**: 8.1 or higher.
* **Bagisto**: v2.*
* **Composer**: 1.6.5 or higher.

## 3. Installation:

- Run the following command
```
composer require codenteq/halkode-payment-gateway
```

- Run these commands below to complete the setup
```
composer dump-autoload
```

> WARNING <br>
> It will check existence of the .env file, if it exists then please update the file manually with the below details.
```
HALKODE_MERCHANT_KEY=
HALKODE_APP_SECRET=
HALKODE_BASE_URL=
```

- Run these commands below to complete the setup
```
php artisan optimize
```

- Publish the assets using the command below
```
php artisan vendor:publish --tag=halkode-assets
```

## Installation without composer:

- To ensure that your custom shipping method package is properly integrated into the Bagisto application, you need to register your service provider. This can be done by adding it to the `bootstrap/providers.php` file in the Bagisto root directory.

```
Webkul\Halkode\Providers\HalkodeServiceProvider::class,
```

- Goto composer.json file and add following line under 'psr-4'

```
"Webkul\\Halkode\\": "packages/Webkul/Halkode/src"
```

- Run these commands below to complete the setup

```
composer dump-autoload
```

> WARNING <br>
> It will check existence of the .env file, if it exists then please update the file manually with the below details.
```
HALKODE_MERCHANT_KEY=
HALKODE_APP_SECRET=
HALKODE_BASE_URL=
```

- Run these commands below to complete the setup
```
php artisan optimize
```

- Publish the assets using the command below
```
php artisan vendor:publish --tag=halkode-assets
```

> That's it, now just execute the project on your specified domain.

## How to contribute
Halk Öde Payment Gateway is always open for direct contributions. Contributions can be in the form of design suggestions, documentation improvements, new component suggestions, code improvements, adding new features or fixing problems. For more information please check our [Contribution Guideline document.](https://codenteq.com/contributor-covenant-code-of-conduct/)

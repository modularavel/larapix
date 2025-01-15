# modularavel/larapix

[![Latest Version on Packagist](https://img.shields.io/packagist/v/modularavel/larapix.svg?style=flat-square)](https://packagist.org/packages/modularavel/larapix)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/modularavel/larapix/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/modularavel/larapix/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/modularavel/larapix/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/modularavel/larapix/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/modularavel/larapix.svg?style=flat-square)](https://packagist.org/packages/modularavel/larapix)

## Demo

[<img src="https://github.com/modularavel/larapix/blob/52918aea761d45d57afb2e0f6b8227d328bc9b3d/screens/1.jpeg" width="100%" />](https://spatie.be/github-ad-click/larapix)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require modularavel/larapix
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="larapix-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="larapix-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="larapix-views"
```

## Usage

```php
$larapix = new Modularavel\Larapix();
echo $larapix->echoPhrase('Hello, Modularavel!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Casimiro Rocha](https://github.com/casimirorocha)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

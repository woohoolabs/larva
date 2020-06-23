# Woohoo Labs. Larva

[![Latest Version on Packagist][ico-version]][link-version]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-build]][link-build]
[![Coverage Status][ico-coverage]][link-coverage]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]
[![Gitter][ico-support]][link-support]

**Woohoo Labs. Larva is an efficient and minimal database abstraction library.**

## Table of Contents

* [Introduction](#introduction)
* [Install](#install)
* [Basic Usage](#basic-usage)
* [Advanced Usage](#advanced-usage)
* [Examples](#examples)
* [Versioning](#versioning)
* [Change Log](#change-log)
* [Contributing](#contributing)
* [Support](#support)
* [Credits](#credits)
* [License](#license)

## Introduction

## Install

The steps of this process are quite straightforward. The only thing you need is [Composer](https://getcomposer.org).

#### Add Larva to your composer.json:

To install this library, run the command below and you will get the latest version:

```bash
$ composer require woohoolabs/larva
```

> Note: The tests and examples won't be downloaded by default. You have to use `composer require woohoolabs/larva --prefer-source`
or clone the repository if you need them.

## Basic Usage

## Advanced Usage

## Examples

Have a look at the [examples directory](https://github.com/woohoolabs/larva/blob/master/examples/) for a really basic
example. Don't forget to run `composer install` first in Larva's root directory if you want to try it out!

## Versioning

This library follows [SemVer v2.0.0](https://semver.org/).

## Change Log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

Larva has a PHPUnit test suite. To run the tests, run the following command from the project folder:

``` bash
$ phpunit
```

Additionally, you may run `docker-compose up` or `make test` to execute the tests.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Support

Please see [SUPPORT](SUPPORT.md) for details.

## Credits

- [Máté Kocsis][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see the [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/woohoolabs/larva.svg
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg
[ico-build]: https://img.shields.io/github/workflow/status/woohoolabs/larva/Continuous%20Integration
[ico-coverage]: https://img.shields.io/codecov/c/github/woohoolabs/larva
[ico-code-quality]: https://img.shields.io/scrutinizer/g/woohoolabs/larva.svg
[ico-downloads]: https://img.shields.io/packagist/dt/woohoolabs/larva.svg
[ico-support]: https://badges.gitter.im/woohoolabs/larva.svg

[link-version]: https://packagist.org/packages/woohoolabs/larva
[link-build]: https://github.com/woohoolabs/larva/actions
[link-coverage]: https://codecov.io/gh/woohoolabs/larva
[link-code-quality]: https://scrutinizer-ci.com/g/woohoolabs/larva
[link-downloads]: https://packagist.org/packages/woohoolabs/larva
[link-author]: https://github.com/kocsismate
[link-contributors]: ../../contributors
[link-support]: https://gitter.im/woohoolabs/larva?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge

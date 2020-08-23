contao-nc-mailjet-sms
=====================

[![Latest Version on Packagist][ico-version]][link-packagist]

Provides an SMS Gateway to Notification Center. Allows you to send SMS through the Mailjet API.

## Install

Via Composer

``` bash
$ composer require richardhj/contao-nc-mailjet-sms
```

## Configuration

Instead of configuring the Mailjet API token per gateway, you can also define the access token in an environment variable:

```dotenv
MAILJETSMS_TOKEN=aabbccddeeff
```


[ico-version]: https://img.shields.io/packagist/v/richardhj/contao-nc-mailjet-sms.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/richardhj/contao-nc-mailjet-sms

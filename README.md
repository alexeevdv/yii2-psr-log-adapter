# PSR3 log adapter for Yii2 logger

[![Build Status](https://travis-ci.org/alexeevdv/yii2-psr-log-adapter.svg?branch=master)](https://travis-ci.org/alexeevdv/yii2-psr-log-adapter) 
[![codecov](https://codecov.io/gh/alexeevdv/yii2-psr-log-adapter/branch/master/graph/badge.svg)](https://codecov.io/gh/alexeevdv/yii2-psr-log-adapter)
![PHP 5.6](https://img.shields.io/badge/PHP-5.6-green.svg)
![PHP 7.0](https://img.shields.io/badge/PHP-7.0-green.svg) 
![PHP 7.1](https://img.shields.io/badge/PHP-7.1-green.svg) 
![PHP 7.2](https://img.shields.io/badge/PHP-7.2-green.svg)


Yii2 logger is not PSR3 compatible, therefore when you need logger functionality in third party library (which uses PSR3 logger interface), this package may save your time.

## Installation

The preferred way to install this extension is through [composer](https://getcomposer.org/download/).

Either run

```bash
$ php composer.phar require alexeevdv/yii2-psr-log-adapter "~1.0"
```

or add

```
"alexeevdv/yii2-psr-log-adapter": "~1.0"
```

to the ```require``` section of your `composer.json` file.

## Usage

Lets assume some third party code
```php
use Psr\Log\LoggerInterface;

class ThirdParty 
{
    private $logger;

    function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
```

### Create adapter implicitly
```php
use alexeevdv\yii\PsrLoggerAdapter;

$logger = new PsrLoggerAdapter(['category' => 'my-category']);
$thirdParty = new ThirdParty($logger);
```

### Transparent usage via DI container
```php
// Yii application config
[
    //...
    'container' => [
        'definitions' => [
            \Psr\Log\LoggerInterface::class => [
                'class' => \alexeevdv\yii\PsrLoggerAdapter::class,
                'category' => 'my-category',
            ],
        ],
    ],
    //...
]

// Lest create third party object now
// Logger adapter will be injected automagically
$thirdParty = Yii::createObject(ThirdParty::class);
```

## Configuration
By default yii logger is taken from DI container but you can specify your own if you wish.

```php
use alexeevdv\yii\PsrLoggerAdapter;

$logger = new PsrLoggerAdapter([
    'logger' => 'mylogger', // logger configuration here. Anything that can be passed to \yii\di\Instance::ensure
    'category' => 'my-category',
]);
```

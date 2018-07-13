# PSR3 log adapter for Yii2 logger

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
```
<?php

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
```
use alexeevdv\yii\PsrLoggerAdapter;

$logger = new PsrLogAdapter(Yii::$app->log->logger, 'my-category');
$thirdParty = new ThirdParty($logger);
```

### Transparent usage via DI container
```
// Yii application config
[
    //...
    'container' => [
        'definitions' => [
            \Psr\LoLoggerInterface::class => \alexeevdv\yii\PsrLoggerAdapter::class,
        ],
    ],
    //...
]

// Lest create third party object now
// Logger adapter will be injected automagically
$thirdParty = Yii::createObject(ThirdParty::class);
```
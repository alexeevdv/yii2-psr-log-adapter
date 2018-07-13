<?php

namespace alexeevdv\yii;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\log\Logger;

/**
 * Class PsrLoggerAdapter
 * @package alexeevdv\yii
 */
class PsrLoggerAdapter extends BaseObject implements LoggerInterface
{
    use LoggerTrait;

    /**
     * @var string
     */
    public $category = 'application';

    /**
     * @var string|array|Logger
     */
    public $logger = Logger::class;

    /**
     * @var array
     */
    public $errorLevelMap = [
        LogLevel::EMERGENCY => Logger::LEVEL_ERROR,
        LogLevel::ALERT => Logger::LEVEL_ERROR,
        LogLevel::CRITICAL => Logger::LEVEL_ERROR,
        LogLevel::ERROR => Logger::LEVEL_ERROR,
        LogLevel::WARNING => Logger::LEVEL_WARNING,
        LogLevel::NOTICE => Logger::LEVEL_INFO,
        LogLevel::INFO => Logger::LEVEL_INFO,
        LogLevel::DEBUG => Logger::LEVEL_TRACE,
    ];

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->logger = Instance::ensure($this->logger, Logger::class);
    }

    /**
     * @inheritdoc
     */
    public function log($level, $message, array $context = array())
    {
        $errorLevel = ArrayHelper::getValue($this->errorLevelMap, $level, Logger::LEVEL_INFO);
        $this->logger->log($message, $errorLevel, $this->category);
    }
}
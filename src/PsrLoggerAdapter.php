<?php

namespace alexeevdv\yii;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use yii\helpers\ArrayHelper;
use yii\log\Logger;

/**
 * Class PsrLoggerAdapter
 * @package alexeevdv\yii
 */
class PsrLoggerAdapter extends AbstractLogger implements LoggerInterface
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var string
     */
    private $category;

    /**
     * @var array
     */
    private $errorLevelMap = [
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
     * PsrLoggerAdapter constructor.
     * @param Logger $logger
     * @param string $category
     */
    public function __construct(Logger $logger, $category = 'application')
    {
        $this->logger = $logger;
        $this->category = $category;
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
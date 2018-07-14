<?php

namespace alexeevdv\yii;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
use yii\base\BaseObject;
use yii\base\ErrorHandler;
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
     * @var string|array|Logger
     */
    public $errorHandler = 'errorHandler';

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
    public function log($level, $message, array $context = [])
    {
        $exception = ArrayHelper::getValue($context, 'exception');
        if ($exception && $exception instanceof Exception) {
            unset($context['exception']);
            $this->logException($exception);
        }

        $errorLevel = ArrayHelper::getValue($this->errorLevelMap, $level, Logger::LEVEL_INFO);
        $formattedMessage = $this->formatMessage($message, $context);
        $this->logger->log($formattedMessage, $errorLevel, $this->category);
    }

    /**
     * @param string $message
     * @param array $context
     * @return string
     */
    protected function formatMessage($message, array $context = [])
    {
        // build a replacement array with braces around the context keys
        $replacements = [];
        foreach ($context as $key => $value) {
            // check that the value can be casted to string
            if (!is_array($value) && (!is_object($value) || method_exists($value, '__toString'))) {
                $replacements['{' . $key . '}'] = $value;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replacements);
    }

    /**
     * @param Exception $exception
     */
    protected function logException(Exception $exception)
    {
        /** @var ErrorHandler $errorHandler */
        try {
            $errorHandler = Instance::ensure($this->errorHandler, ErrorHandler::class);
            $errorHandler->logException($exception);
        } catch (Exception $e) {
        }
    }
}

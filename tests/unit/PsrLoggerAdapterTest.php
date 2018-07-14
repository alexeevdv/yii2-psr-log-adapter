<?php

namespace tests\unit;

use alexeevdv\yii\PsrLoggerAdapter;
use Codeception\Stub;
use Psr\Log\LogLevel;
use Yii;
use yii\base\InvalidConfigException;
use yii\log\Logger;
use yii\web\ErrorHandler;

/**
 * Class PsrLoggerAdapterTest
 * @package tests\unit
 */
class PsrLoggerAdapterTest extends \Codeception\Test\Unit
{
    /**
     * @inheritdoc
     */
    public function _after()
    {
        Yii::$container->clear('customLogger');
    }

    /**
     * @test
     */
    public function yiiLoggerDefaultInstantination()
    {
        $adapter = new PsrLoggerAdapter;
        $this->assertInstanceOf(Logger::class, $adapter->logger);
    }

    /**
     * @test
     */
    public function yiiLoggerInstantiationFromComponent()
    {
        Yii::$container->set('customLogger', ['class' => Logger::class]);
        $adapter = new PsrLoggerAdapter(['logger' => 'customLogger']);
        $this->assertInstanceOf(Logger::class, $adapter->logger);
    }

    /**
     * @test
     */
    public function yiiLoggerInstantinationFromArray()
    {
        $adapter = new PsrLoggerAdapter(['logger' => ['class' => Logger::class]]);
        $this->assertInstanceOf(Logger::class, $adapter->logger);
    }

    /**
     * @test
     * @dataProvider errorLevelsMappingDataProvider
     * @param string $psrLevel
     * @param string $expectedYiiLevel
     */
    public function errorLevelsMapping($psrLevel, $expectedYiiLevel)
    {
        $logger = Stub::makeEmpty(Logger::class, [
            'log' => function ($message, $level, $category) use ($expectedYiiLevel) {
                $this->assertEquals('Error levels mapping test', $message);
                $this->assertEquals($expectedYiiLevel, $level);
                $this->assertEquals($category, 'test');
            }
        ], $this);

        $adapter = new PsrLoggerAdapter(['logger' => $logger, 'category' => 'test']);
        $adapter->log($psrLevel, 'Error levels mapping test');
    }

    /**
     * @return array
     */
    public function errorLevelsMappingDataProvider()
    {
        return [
            'emergency' => [LogLevel::EMERGENCY, Logger::LEVEL_ERROR],
            'alert' => [LogLevel::ALERT, Logger::LEVEL_ERROR],
            'critical' => [LogLevel::CRITICAL, Logger::LEVEL_ERROR],
            'error' => [LogLevel::ERROR, Logger::LEVEL_ERROR],
            'warning' => [LogLevel::WARNING, Logger::LEVEL_WARNING],
            'notice' => [LogLevel::NOTICE, Logger::LEVEL_INFO],
            'info' => [LogLevel::INFO, Logger::LEVEL_INFO],
            'debug' => [LogLevel::DEBUG, Logger::LEVEL_TRACE],
        ];
    }

    /**
     * @test
     */
    public function exceptionHandling()
    {
        $logger = Stub::makeEmpty(Logger::class);
        $errorHandler = Stub::makeEmpty(ErrorHandler::class, [
            'logException' => function ($exception) {
                $this->assertInstanceOf(InvalidConfigException::class, $exception);
            }
        ], $this);
        $adapter = new PsrLoggerAdapter([
            'logger' => $logger,
            'errorHandler' => $errorHandler
        ]);
        $adapter->log(
            LogLevel::CRITICAL,
            'Exception handling test',
            ['exception' => new InvalidConfigException]
        );
    }

    /**
     * @test
     */
    public function exceptionHandlingWithCorruptedErrorHandler()
    {
        $logger = Stub::makeEmpty(Logger::class);
        $errorHandler = ['this is not gonna work'];
        $adapter = new PsrLoggerAdapter(['logger' => $logger, 'errorHandler' => $errorHandler]);
        $adapter->log(
            LogLevel::CRITICAL,
            'Exception handling test',
            ['exception' => new InvalidConfigException]
        );
    }

    /**
     * @test
     */
    public function errorMessagePlaceholders()
    {
        $logger = Stub::makeEmpty(Logger::class, [
            'log' => function ($message) {
                $this->assertEquals('xxx is yyy', $message);
            }
        ], $this);

        $adapter = new PsrLoggerAdapter(['logger' => $logger]);
        $adapter->log(LogLevel::INFO, '{x} is {y}', ['x' => 'xxx', 'y' => 'yyy']);
    }
}

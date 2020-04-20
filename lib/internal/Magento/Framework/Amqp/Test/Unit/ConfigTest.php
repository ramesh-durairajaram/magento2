<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Amqp\Test\Unit;

use LogicException;
use Magento\Framework\Amqp\Config;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var DeploymentConfig|MockObject
     */
    private $deploymentConfigMock;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Config
     */
    private $amqpConfig;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->deploymentConfigMock = $this->getMockBuilder(DeploymentConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(['getConfigData'])
            ->getMock();
        $this->amqpConfig = $this->objectManager->getObject(
            Config::class,
            [
                'config' => $this->deploymentConfigMock,
            ]
        );
    }

    /**
     * @return void
     * @throws Exception
     * @throws LogicException
     */
    public function testGetNullConfig(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Unknown connection name amqp');

        $this->deploymentConfigMock->expects($this->once())
            ->method('getConfigData')
            ->with(Config::QUEUE_CONFIG)
            ->will($this->returnValue(null));

        $this->amqpConfig->getValue(Config::HOST);
    }

    /**
     * @return void
     * @throws Exception
     * @throws LogicException
     */
    public function testGetEmptyConfig(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Unknown connection name amqp');

        $this->deploymentConfigMock->expects($this->once())
            ->method('getConfigData')
            ->with(Config::QUEUE_CONFIG)
            ->will($this->returnValue([]));

        $this->amqpConfig->getValue(Config::HOST);
    }

    /**
     * @return void
     */
    public function testGetStandardConfig(): void
    {
        $expectedHost = 'example.com';
        $expectedPort = 5672;
        $expectedUsername = 'guest_username';
        $expectedPassword = 'guest_password';
        $expectedVirtualHost = '/';
        $expectedSsl = false;
        $expectedSslOptions = ['some' => 'value'];

        $this->deploymentConfigMock->expects($this->once())
            ->method('getConfigData')
            ->with(Config::QUEUE_CONFIG)
            ->will($this->returnValue(
                [
                    Config::AMQP_CONFIG => [
                        'host' => $expectedHost,
                        'port' => $expectedPort,
                        'user' => $expectedUsername,
                        'password' => $expectedPassword,
                        'virtualhost' => $expectedVirtualHost,
                        'ssl' => $expectedSsl,
                        'ssl_options' => $expectedSslOptions,
                        'randomKey' => 'randomValue',
                    ]
                ]
            ));

        $this->assertEquals($expectedHost, $this->amqpConfig->getValue(Config::HOST));
        $this->assertEquals($expectedPort, $this->amqpConfig->getValue(Config::PORT));
        $this->assertEquals($expectedUsername, $this->amqpConfig->getValue(Config::USERNAME));
        $this->assertEquals($expectedPassword, $this->amqpConfig->getValue(Config::PASSWORD));
        $this->assertEquals($expectedVirtualHost, $this->amqpConfig->getValue(Config::VIRTUALHOST));
        $this->assertEquals($expectedSsl, $this->amqpConfig->getValue(Config::SSL));
        $this->assertEquals($expectedSslOptions, $this->amqpConfig->getValue(Config::SSL_OPTIONS));
        $this->assertEquals('randomValue', $this->amqpConfig->getValue('randomKey'));
    }

    /**
     * @return void
     */
    public function testGetCustomConfig(): void
    {
        $amqpConfig = new Config($this->deploymentConfigMock, 'connection-01');
        $expectedHost = 'example.com';
        $expectedPort = 5672;
        $expectedUsername = 'guest_username';
        $expectedPassword = 'guest_password';
        $expectedVirtualHost = '/';
        $expectedSsl = ['some' => 'value'];

        $this->deploymentConfigMock->expects($this->once())
            ->method('getConfigData')
            ->with(Config::QUEUE_CONFIG)
            ->will($this->returnValue(
                [
                    'connections' => [
                        'connection-01' => [
                            'host' => $expectedHost,
                            'port' => $expectedPort,
                            'user' => $expectedUsername,
                            'password' => $expectedPassword,
                            'virtualhost' => $expectedVirtualHost,
                            'ssl' => $expectedSsl,
                            'randomKey' => 'randomValue',
                        ]
                    ]
                ]
            ));

        $this->assertEquals($expectedHost, $amqpConfig->getValue(Config::HOST));
        $this->assertEquals($expectedPort, $amqpConfig->getValue(Config::PORT));
        $this->assertEquals($expectedUsername, $amqpConfig->getValue(Config::USERNAME));
        $this->assertEquals($expectedPassword, $amqpConfig->getValue(Config::PASSWORD));
        $this->assertEquals($expectedVirtualHost, $amqpConfig->getValue(Config::VIRTUALHOST));
        $this->assertEquals($expectedSsl, $amqpConfig->getValue(Config::SSL));
        $this->assertEquals('randomValue', $amqpConfig->getValue('randomKey'));
    }
}

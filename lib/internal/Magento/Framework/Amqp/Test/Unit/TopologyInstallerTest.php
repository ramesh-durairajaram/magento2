<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Amqp\Test\Unit;

use Magento\Framework\Amqp\TopologyInstaller;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\MessageQueue\Topology\ConfigInterface;
use PhpAmqpLib\Exception\AMQPLogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Unit tests for @see TopologyInstaller
 */
class TopologyInstallerTest extends TestCase
{
    /**
     * @var TopologyInstaller
     */
    private $topologyInstaller;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ConfigInterface|MockObject
     */
    private $topologyConfigMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * Initialize topology installer.
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->topologyConfigMock = $this->createMock(ConfigInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->topologyInstaller = $this->objectManager->getObject(
            TopologyInstaller::class,
            ['topologyConfig' => $this->topologyConfigMock, 'logger' => $this->loggerMock]
        );
        parent::setUp();
    }

    /**
     * Make sure that topology creation errors in log contain actual error message.
     * @return void
     */
    public function testInstallException(): void
    {
        $exceptionMessage = "Exception message";

        $this->topologyConfigMock
            ->expects($this->once())
            ->method('getQueues')
            ->willThrowException(new AMQPLogicException($exceptionMessage));

        $this->loggerMock
            ->expects($this->once())
            ->method('error')
            ->with($this->stringContains("AMQP topology installation failed: {$exceptionMessage}"));

        $this->topologyInstaller->install();
    }
}

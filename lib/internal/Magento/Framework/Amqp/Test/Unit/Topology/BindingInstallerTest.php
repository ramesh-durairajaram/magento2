<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Amqp\Test\Unit\Topology;

use Magento\Framework\Amqp\Topology\BindingInstaller;
use Magento\Framework\Amqp\Topology\BindingInstallerInterface;
use Magento\Framework\MessageQueue\Topology\Config\ExchangeConfigItem\BindingInterface;
use PHPUnit\Framework\TestCase;
use PhpAmqpLib\Channel\AMQPChannel;

class BindingInstallerTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstall(): void
    {
        $installerOne = $this->createMock(BindingInstallerInterface::class);
        $installerTwo = $this->createMock(BindingInstallerInterface::class);
        $model = new BindingInstaller(
            [
                'queue' => $installerOne,
                'exchange' => $installerTwo,
            ]
        );
        $channel = $this->createMock(AMQPChannel::class);
        $binding = $this->createMock(BindingInterface::class);
        $binding->expects($this->once())->method('getDestinationType')->willReturn('queue');
        $installerOne->expects($this->once())->method('install')->with($channel, $binding, 'magento');
        $installerTwo->expects($this->never())->method('install');
        $model->install($channel, $binding, 'magento');
    }

    /**
     * @return void
     */
    public function testInstallInvalidType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Installer type [test] is not configured');

        $installerOne = $this->createMock(BindingInstallerInterface::class);
        $installerTwo = $this->createMock(BindingInstallerInterface::class);
        $model = new BindingInstaller(
            [
                'queue' => $installerOne,
                'exchange' => $installerTwo,
            ]
        );
        $channel = $this->createMock(AMQPChannel::class);
        $binding = $this->createMock(BindingInterface::class);
        $binding->expects($this->once())->method('getDestinationType')->willReturn('test');
        $installerOne->expects($this->never())->method('install');
        $installerTwo->expects($this->never())->method('install');
        $model->install($channel, $binding, 'magento');
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Amqp\Test\Unit\Topology\BindingInstallerType;

use Magento\Framework\Amqp\Topology\BindingInstallerType\Exchange;
use Magento\Framework\MessageQueue\Topology\Config\ExchangeConfigItem\BindingInterface;
use PHPUnit\Framework\TestCase;
use PhpAmqpLib\Channel\AMQPChannel;

class ExchangeTest extends TestCase
{
    /**
     * @var Exchange
     */
    private $model;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->model = new Exchange();
    }

    /**
     * @return void
     */
    public function testInstall(): void
    {
        $channel = $this->createMock(AMQPChannel::class);
        $binding = $this->createMock(BindingInterface::class);
        $binding->expects($this->once())->method('getDestination')->willReturn('queue01');
        $binding->expects($this->once())->method('getTopic')->willReturn('topic01');
        $binding->expects($this->once())->method('getArguments')->willReturn(['some' => 'value']);

        $channel->expects($this->once())
            ->method('exchange_bind')
            ->with(
                'queue01',
                'magento',
                'topic01',
                false,
                ['some' => ['S', 'value']],
                null
            );
        $this->model->install($channel, $binding, 'magento');
    }
}

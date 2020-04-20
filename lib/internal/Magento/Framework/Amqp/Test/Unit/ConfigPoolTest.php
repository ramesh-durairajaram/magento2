<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Amqp\Test\Unit;

use Magento\Framework\Amqp\Config as AmqpConfig;
use Magento\Framework\Amqp\ConfigFactory as AmqpConfigFactory;
use Magento\Framework\Amqp\ConfigPool as AmqpConfigPool;
use PHPUnit\Framework\TestCase;

class ConfigPoolTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetConnection(): void
    {
        $factory = $this->createMock(AmqpConfigFactory::class);
        $config = $this->createMock(AmqpConfig::class);
        $factory->expects($this->once())->method('create')->with(['connectionName' => 'amqp'])->willReturn($config);
        $model = new AmqpConfigPool($factory);
        $this->assertEquals($config, $model->get('amqp'));
        //test that object is cached
        $this->assertEquals($config, $model->get('amqp'));
    }
}

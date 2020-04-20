<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Amqp\Test\Unit\Bulk;

use Magento\Framework\Amqp\Bulk\Exchange as BulkExchange;
use Magento\Framework\Amqp\Config as AmqpConfig;
use Magento\Framework\Amqp\Exchange;
use Magento\Framework\Communication\ConfigInterface as CommunicationConfig;
use Magento\Framework\MessageQueue\EnvelopeInterface as Envelope;
use Magento\Framework\MessageQueue\Publisher\ConfigInterface as PublisherConfig;
use Magento\Framework\MessageQueue\Publisher\Config\PublisherConfigItemInterface as Publisher;
use Magento\Framework\MessageQueue\Publisher\Config\PublisherConnectionInterface as PublisherConnection;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for Exchange model.
 */
class ExchangeTest extends TestCase
{
    /**
     * @var AmqpConfig|MockObject
     */
    private $amqpConfig;

    /**
     * @var CommunicationConfig|MockObject
     */
    private $communicationConfig;

    /**
     * @var PublisherConfig|MockObject
     */
    private $publisherConfig;

    /**
     * @var Exchange|MockObject
     */
    private $exchange;

    /**
     * @var BulkExchange
     */
    private $bulkExchange;

    /**
     * Set up.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->amqpConfig = $this->getMockBuilder(AmqpConfig::class)
            ->disableOriginalConstructor()->getMock();
        $this->communicationConfig = $this->getMockBuilder(CommunicationConfig::class)
            ->disableOriginalConstructor()->getMock();
        $this->publisherConfig = $this->getMockBuilder(PublisherConfig::class)
            ->disableOriginalConstructor()->getMock();
        $this->exchange = $this->getMockBuilder(Exchange::class)
            ->disableOriginalConstructor()->getMock();

        $objectManager = new ObjectManagerHelper($this);
        $this->bulkExchange = $objectManager->getObject(
            BulkExchange::class,
            [
                'amqpConfig' => $this->amqpConfig,
                'communicationConfig' => $this->communicationConfig,
                'publisherConfig' => $this->publisherConfig,
                'exchange' => $this->exchange,
            ]
        );
    }

    /**
     * Test for enqueue method.
     *
     * @return void
     */
    public function testEnqueue(): void
    {
        $topicName = 'topic.name';
        $exchangeName = 'exchangeName';
        $envelopeBody = 'envelopeBody';
        $envelopeProperties = ['property_key_1' => 'property_value_1'];
        $topicData = [CommunicationConfig::TOPIC_IS_SYNCHRONOUS => false];
        $this->communicationConfig->expects($this->once())
            ->method('getTopic')->with($topicName)->willReturn($topicData);
        $channel = $this->getMockBuilder(\AMQPChannel::class)
                        ->setMethods(['batch_basic_publish', 'publish_batch'])
                        ->disableOriginalConstructor()->getMock();
        $this->amqpConfig->expects($this->once())->method('getChannel')->willReturn($channel);
        $publisher = $this->getMockBuilder(Publisher::class)
                          ->disableOriginalConstructor()->getMock();
        $this->publisherConfig->expects($this->once())
            ->method('getPublisher')->with($topicName)->willReturn($publisher);
        $connection = $this->getMockBuilder(PublisherConnection::class)
                           ->disableOriginalConstructor()->getMock();
        $publisher->expects($this->once())->method('getConnection')->with()->willReturn($connection);
        $connection->expects($this->once())->method('getExchange')->with()->willReturn($exchangeName);
        $envelope = $this->getMockBuilder(Envelope::class)
                         ->disableOriginalConstructor()->getMock();
        $envelope->expects($this->once())->method('getBody')->willReturn($envelopeBody);
        $envelope->expects($this->once())->method('getProperties')->willReturn($envelopeProperties);
        $channel->expects($this->once())->method('batch_basic_publish')
                ->with($this->isInstanceOf(AMQPMessage::class), $exchangeName, $topicName);
        $channel->expects($this->once())->method('publish_batch');
        $this->assertNull($this->bulkExchange->enqueue($topicName, [$envelope]));
    }

    /**
     * Test for enqueue method with synchronous topic.
     *
     * @return void
     */
    public function testEnqueueWithSynchronousTopic(): void
    {
        $topicName = 'topic.name';
        $response = 'responseBody';
        $topicData = [CommunicationConfig::TOPIC_IS_SYNCHRONOUS => true];
        $this->communicationConfig->expects($this->once())
            ->method('getTopic')->with($topicName)->willReturn($topicData);
        $envelope = $this->getMockBuilder(Envelope::class)->disableOriginalConstructor()->getMock();
        $this->exchange->expects($this->once())->method('enqueue')->with($topicName, $envelope)->willReturn($response);
        $this->assertEquals([$response], $this->bulkExchange->enqueue($topicName, [$envelope]));
    }
}

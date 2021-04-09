<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use webignition\SymfonyMessengerMessageDispatcher\MessageDispatcher;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\DelayedMessage\DelayedMessageMiddleware;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\DelayedMessage\FixedBackoffStrategy;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\IgnoredMessageMiddleware;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\MiddlewareInterface;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\RetryByLimitMiddleware;
use webignition\SymfonyMessengerMessageDispatcher\Stamp\NonDispatchableStamp;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\Message;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\RetryableMessage;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Services\MessageBus;

class MessageDispatcherTest extends TestCase
{
    /**
     * @dataProvider dispatchIsDispatchedDataProvider
     *
     * @param MiddlewareInterface[] $middleware
     */
    public function testDispatchIsDispatched(array $middleware, object $message, Envelope $expectedEnvelope): void
    {
        $messageBus = new MessageBus();

        $messageDispatcher = new MessageDispatcher($messageBus, $middleware);

        $returnedEnvelope = $messageDispatcher->dispatch($message);
        self::assertEquals($expectedEnvelope, $returnedEnvelope);

        $messageBusStack = $messageBus->getStack();

        self::assertEquals($expectedEnvelope, $messageBusStack[0]);
    }

    /**
     * @return array[]
     */
    public function dispatchIsDispatchedDataProvider(): array
    {
        return [
            'no middleware' => [
                'middleware' => [],
                'message' => new Message(),
                'expectedEnvelope' => Envelope::wrap(new Message()),
            ],
            'delayed message middleware' => [
                'middleware' => [
                    new DelayedMessageMiddleware([
                        Message::class => new FixedBackoffStrategy(1000),
                    ])
                ],
                'message' => new Message(),
                'expectedEnvelope' => Envelope::wrap(
                    new Message(),
                    [
                        new DelayStamp(1000)
                    ]
                ),
            ],
        ];
    }

    /**
     * @dataProvider dispatchNotDispatchedDataProvider
     *
     * @param MiddlewareInterface[] $middleware
     */
    public function testDispatchNotDispatched(array $middleware, object $message, Envelope $expectedEnvelope): void
    {
        $messageBus = new MessageBus();

        $messageDispatcher = new MessageDispatcher($messageBus, $middleware);

        $returnedEnvelope = $messageDispatcher->dispatch($message);
        self::assertEquals($expectedEnvelope, $returnedEnvelope);

        $messageBusStack = $messageBus->getStack();

        self::assertSame([], $messageBusStack);
    }

    /**
     * @return array[]
     */
    public function dispatchNotDispatchedDataProvider(): array
    {
        return [
            'ignored middleware' => [
                'middleware' => [
                    new IgnoredMessageMiddleware([
                        Message::class,
                    ])
                ],
                'message' => new Message(),
                'expectedEnvelope' => Envelope::wrap(
                    new Message(),
                    [
                        new NonDispatchableStamp(IgnoredMessageMiddleware::REASON),
                    ]
                ),
            ],
            'retry by limit middleware' => [
                'middleware' => [
                    new RetryByLimitMiddleware([
                        RetryableMessage::class => 2,
                    ])
                ],
                'message' => new RetryableMessage(3),
                'expectedEnvelope' => Envelope::wrap(
                    new RetryableMessage(3),
                    [
                        new NonDispatchableStamp(RetryByLimitMiddleware::REASON),
                    ]
                ),
            ],
        ];
    }

    /**
     * @dataProvider isDispatchableDataProvider
     */
    public function testIsDispatchable(Envelope $envelope, bool $expectedIsDispatchable): void
    {
        self::assertSame($expectedIsDispatchable, MessageDispatcher::isDispatchable($envelope));
    }

    /**
     * @return array[]
     */
    public function isDispatchableDataProvider(): array
    {
        return [
            'dispatchable' => [
                'envelope' => Envelope::wrap(new Message()),
                'expectedIsDispatchable' => true,
            ],
            'not dispatchable' => [
                'envelope' => Envelope::wrap(
                    new Message(),
                    [
                        new NonDispatchableStamp(IgnoredMessageMiddleware::REASON),
                    ]
                ),
                'expectedIsDispatchable' => false,
            ],
        ];
    }
}

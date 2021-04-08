<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use webignition\SymfonyMessengerMessageDispatcher\MessageDispatcher;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\DelayedMessageMiddleware;
use webignition\SymfonyMessengerMessageDispatcher\MiddlewareInterface;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\MessageOne;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Services\MessageBus;

class MessageDispatcherTest extends TestCase
{
    /**
     * @dataProvider dispatchDataProvider
     *
     * @param MiddlewareInterface[] $middleware
     */
    public function testDispatch(array $middleware, object $message, Envelope $expectedEnvelope): void
    {
        $messageBus = new MessageBus();

        $messageDispatcher = new MessageDispatcher($messageBus, $middleware);

        $messageDispatcher->dispatch($message);

        $messageBusStack = $messageBus->getStack();

        self::assertEquals($expectedEnvelope, $messageBusStack[0]);
    }

    /**
     * @return array[]
     */
    public function dispatchDataProvider(): array
    {
        return [
            'no middleware' => [
                'middleware' => [],
                'message' => new MessageOne(),
                'expectedEnvelope' => Envelope::wrap(new MessageOne()),
            ],
            'delayed message middleware' => [
                'middleware' => [
                    new DelayedMessageMiddleware([
                        MessageOne::class => 1000,
                    ])
                ],
                'message' => new MessageOne(),
                'expectedEnvelope' => Envelope::wrap(
                    new MessageOne(),
                    [
                        new DelayStamp(1000)
                    ]
                ),
            ],
        ];
    }
}

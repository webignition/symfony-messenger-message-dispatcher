<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Tests\Unit\Middleware;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\DelayedMessageMiddleware;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\Message;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\RetryableMessage;

class DelayedMessageMiddlewareTest extends TestCase
{
    /**
     * @dataProvider invokeDataProvider
     *
     * @param array<class-string, int> $delays
     */
    public function testInvoke(array $delays, Envelope $envelope, Envelope $expectedEnvelope): void
    {
        $middleware = new DelayedMessageMiddleware($delays);

        $handledEnvelope = ($middleware)($envelope);

        self::assertEquals($expectedEnvelope, $handledEnvelope);
    }

    /**
     * @return array[]
     */
    public function invokeDataProvider(): array
    {
        return [
            'no delays' => [
                'delays' => [],
                'envelope' => Envelope::wrap(new Message()),
                'expectedEnvelope' => Envelope::wrap(new Message()),
            ],
            'no relevant delays' => [
                'delays' => [
                    RetryableMessage::class => 1000,
                ],
                'envelope' => Envelope::wrap(new Message()),
                'expectedEnvelope' => Envelope::wrap(new Message()),
            ],
            'has relevant delays' => [
                'delays' => [
                    RetryableMessage::class => 1000,
                ],
                'envelope' => Envelope::wrap(new RetryableMessage()),
                'expectedEnvelope' => Envelope::wrap(
                    new RetryableMessage(),
                    [
                        new DelayStamp(1000)
                    ]
                ),
            ],
        ];
    }
}

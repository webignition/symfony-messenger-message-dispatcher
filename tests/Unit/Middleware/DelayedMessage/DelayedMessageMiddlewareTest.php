<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Tests\Unit\Middleware\DelayedMessage;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\DelayedMessage\BackoffStrategyInterface;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\DelayedMessage\DelayedMessageMiddleware;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\DelayedMessage\FixedBackoffStrategy;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\Message;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\RetryableMessage;

class DelayedMessageMiddlewareTest extends TestCase
{
    /**
     * @dataProvider invokeDataProvider
     *
     * @param array<string, BackoffStrategyInterface> $backoffStrategies
     */
    public function testInvoke(array $backoffStrategies, Envelope $envelope, Envelope $expectedEnvelope): void
    {
        $envelope = (new DelayedMessageMiddleware($backoffStrategies))($envelope);

        self::assertEquals($expectedEnvelope, $envelope);
    }

    /**
     * @return array[]
     */
    public function invokeDataProvider(): array
    {
        return [
            'no backoff strategy' => [
                'backoffStrategies' => [],
                'envelope' => Envelope::wrap(new Message()),
                'expectedEnvelope' => Envelope::wrap(new Message()),
            ],
            'no relevant backoff strategy' => [
                'backoffStrategies' => [
                    RetryableMessage::class => new FixedBackoffStrategy(1000),
                ],
                'envelope' => Envelope::wrap(new Message()),
                'expectedEnvelope' => Envelope::wrap(new Message()),
            ],
            'has relevant backoff strategy by class name' => [
                'backoffStrategies' => [
                    RetryableMessage::class => new FixedBackoffStrategy(1000),
                ],
                'envelope' => Envelope::wrap(new RetryableMessage()),
                'expectedEnvelope' => Envelope::wrap(
                    new RetryableMessage(),
                    [
                        new DelayStamp(1000)
                    ]
                ),
            ],
            'has relevant backoff strategy by wildcard' => [
                'backoffStrategies' => [
                    '*' => new FixedBackoffStrategy(1000),
                ],
                'envelope' => Envelope::wrap(new RetryableMessage()),
                'expectedEnvelope' => Envelope::wrap(
                    new RetryableMessage(),
                    [
                        new DelayStamp(1000)
                    ]
                ),
            ],
            'has relevant backoff strategy by class name and wildcard' => [
                'backoffStrategies' => [
                    RetryableMessage::class => new FixedBackoffStrategy(2000),
                    '*' => new FixedBackoffStrategy(1000),
                ],
                'envelope' => Envelope::wrap(new RetryableMessage()),
                'expectedEnvelope' => Envelope::wrap(
                    new RetryableMessage(),
                    [
                        new DelayStamp(2000)
                    ]
                ),
            ],
        ];
    }
}

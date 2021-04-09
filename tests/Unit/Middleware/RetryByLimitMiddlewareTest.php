<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Tests\Unit\Middleware;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\RetryByLimitMiddleware;
use webignition\SymfonyMessengerMessageDispatcher\Stamp\NonDispatchableStamp;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\Message;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\RetryableMessage;

class RetryByLimitMiddlewareTest extends TestCase
{
    /**
     * @dataProvider invokeDataProvider
     *
     * @param array<class-string, int> $retryLimits
     */
    public function testInvoke(array $retryLimits, Envelope $envelope, Envelope $expectedEnvelope): void
    {
        $result = (new RetryByLimitMiddleware($retryLimits))($envelope);

        self::assertEquals($expectedEnvelope, $result);
    }

    /**
     * @return array[]
     */
    public function invokeDataProvider(): array
    {
        return [
            'no retry limits' => [
                'retryLimits' => [],
                'envelope' => Envelope::wrap(new Message()),
                'expectedEnvelope' => Envelope::wrap(new Message()),
            ],
            'no relevant retry limits' => [
                'retryLimits' => [
                    Message::class => 3,
                ],
                'envelope' => Envelope::wrap(new Message()),
                'expectedEnvelope' => Envelope::wrap(new Message()),
            ],
            'has relevant retry limits, limit not reached' => [
                'retryLimits' => [
                    RetryableMessage::class => 3,
                ],
                'envelope' => Envelope::wrap(new RetryableMessage()),
                'expectedEnvelope' => Envelope::wrap(new RetryableMessage()),
            ],
            'has relevant retry limits, limit reached' => [
                'retryLimits' => [
                    RetryableMessage::class => 3,
                ],
                'envelope' => Envelope::wrap(new RetryableMessage(4)),
                'expectedEnvelope' => Envelope::wrap(
                    new RetryableMessage(4),
                    [
                        new NonDispatchableStamp(RetryByLimitMiddleware::REASON),
                    ]
                ),
            ],
        ];
    }
}

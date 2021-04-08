<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Tests\Unit\Middleware;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use webignition\SymfonyMessengerMessageDispatcher\Exception\NonDispatchableMessageException;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\RetryByLimitMiddleware;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\Message;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\RetryableMessage;

class RetryByLimitMiddlewareTest extends TestCase
{
    /**
     * @dataProvider invokeNoExceptionDataProvider
     *
     * @param array<class-string, int> $retryLimits
     */
    public function testInvokeNoException(array $retryLimits, Envelope $envelope, Envelope $expectedEnvelope): void
    {
        $middleware = new RetryByLimitMiddleware($retryLimits);

        $handledEnvelope = ($middleware)($envelope);

        self::assertEquals($expectedEnvelope, $handledEnvelope);
    }

    /**
     * @return array[]
     */
    public function invokeNoExceptionDataProvider(): array
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
            'has relevant retry limits' => [
                'retryLimits' => [
                    RetryableMessage::class => 3,
                ],
                'envelope' => Envelope::wrap(new RetryableMessage()),
                'expectedEnvelope' => Envelope::wrap(new RetryableMessage()),
            ],
        ];
    }

    public function invokeThrowsExceptionTest(): void
    {
        $retryLimit = 3;

        $message = new RetryableMessage($retryLimit);

        $middleware = new RetryByLimitMiddleware([
            RetryableMessage::class => $retryLimit,
        ]);

        self::expectExceptionObject(new NonDispatchableMessageException($message));

        ($middleware)(Envelope::wrap($message));
    }
}

<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Tests\Unit\Middleware;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\Result\Result;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\Result\ResultInterface;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\RetryByLimitMiddleware;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\Message;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\RetryableMessage;

class RetryByLimitMiddlewareTest extends TestCase
{
    /**
     * @dataProvider invokeDataProvider
     *
     * @param array<class-string, int> $retryLimits
     */
    public function testInvoke(array $retryLimits, Envelope $envelope, ResultInterface $expectedResult): void
    {
        $result = (new RetryByLimitMiddleware($retryLimits))($envelope);

        self::assertEquals($expectedResult, $result);
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
                'expectedResult' => Result::createDispatchable(Envelope::wrap(new Message())),
            ],
            'no relevant retry limits' => [
                'retryLimits' => [
                    Message::class => 3,
                ],
                'envelope' => Envelope::wrap(new Message()),
                'expectedResult' => Result::createDispatchable(Envelope::wrap(new Message())),
            ],
            'has relevant retry limits, limit not reached' => [
                'retryLimits' => [
                    RetryableMessage::class => 3,
                ],
                'envelope' => Envelope::wrap(new RetryableMessage()),
                'expectedResult' => Result::createDispatchable(Envelope::wrap(new RetryableMessage())),
            ],
            'has relevant retry limits, limit reached' => [
                'retryLimits' => [
                    RetryableMessage::class => 3,
                ],
                'envelope' => Envelope::wrap(new RetryableMessage(4)),
                'expectedResult' => Result::createNonDispatchable(
                    Envelope::wrap(new RetryableMessage(4)),
                    RetryByLimitMiddleware::REASON
                ),
            ],
        ];
    }
}

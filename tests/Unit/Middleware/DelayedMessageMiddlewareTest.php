<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Tests\Unit\Middleware;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\DelayedMessageMiddleware;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\Result\Result;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\Result\ResultInterface;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\Message;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\RetryableMessage;

class DelayedMessageMiddlewareTest extends TestCase
{
    /**
     * @dataProvider invokeDataProvider
     *
     * @param array<class-string, int> $delays
     */
    public function testInvoke(array $delays, Envelope $envelope, ResultInterface $expectedResult): void
    {
        $result = (new DelayedMessageMiddleware($delays))($envelope);

        self::assertEquals($expectedResult, $result);
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
                'expectedResult' => Result::createDispatchable(Envelope::wrap(new Message())),
            ],
            'no relevant delays' => [
                'delays' => [
                    RetryableMessage::class => 1000,
                ],
                'envelope' => Envelope::wrap(new Message()),
                'expectedResult' => Result::createDispatchable(Envelope::wrap(new Message())),
            ],
            'has relevant delays' => [
                'delays' => [
                    RetryableMessage::class => 1000,
                ],
                'envelope' => Envelope::wrap(new RetryableMessage()),
                'expectedResult' => Result::createDispatchable(Envelope::wrap(
                    new RetryableMessage(),
                    [
                        new DelayStamp(1000)
                    ]
                )),
            ],
        ];
    }
}

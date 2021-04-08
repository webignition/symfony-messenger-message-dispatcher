<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Tests\Unit\Middleware;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\IgnoredMessageMiddleware;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\Result\Result;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\Result\ResultInterface;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\Message;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\RetryableMessage;

class IgnoredMessageMiddlewareTest extends TestCase
{
    /**
     * @dataProvider invokeDataProvider
     *
     * @param array<class-string> $messageClassNames
     */
    public function testInvoke(array $messageClassNames, Envelope $envelope, ResultInterface $expectedResult): void
    {
        $result = (new IgnoredMessageMiddleware($messageClassNames))($envelope);

        self::assertEquals($expectedResult, $result);
    }

    /**
     * @return array[]
     */
    public function invokeDataProvider(): array
    {
        return [
            'no message class names' => [
                'messageClassNames' => [],
                'envelope' => Envelope::wrap(new Message()),
                'expectedResult' => Result::createDispatchable(Envelope::wrap(new Message())),
            ],
            'no matching message class name' => [
                'messageClassNames' => [
                    RetryableMessage::class,
                ],
                'envelope' => Envelope::wrap(new Message()),
                'expectedResult' => Result::createDispatchable(Envelope::wrap(new Message())),
            ],
            'has matching message class name' => [
                'messageClassNames' => [
                    RetryableMessage::class,
                ],
                'envelope' => Envelope::wrap(new RetryableMessage()),
                'expectedResult' => Result::createNonDispatchable(
                    Envelope::wrap(new RetryableMessage()),
                    IgnoredMessageMiddleware::REASON
                ),
            ],
        ];
    }
}

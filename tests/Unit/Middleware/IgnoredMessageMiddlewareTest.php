<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Tests\Unit\Middleware;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\IgnoredMessageMiddleware;
use webignition\SymfonyMessengerMessageDispatcher\Stamp\NonDispatchableStamp;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\Message;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\RetryableMessage;

class IgnoredMessageMiddlewareTest extends TestCase
{
    /**
     * @dataProvider invokeDataProvider
     *
     * @param array<class-string> $messageClassNames
     */
    public function testInvoke(array $messageClassNames, Envelope $envelope, Envelope $expectedEnvelope): void
    {
        $result = (new IgnoredMessageMiddleware($messageClassNames))($envelope);

        self::assertEquals($expectedEnvelope, $result);
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
                'expectedEnvelope' => Envelope::wrap(new Message()),
            ],
            'no matching message class name' => [
                'messageClassNames' => [
                    RetryableMessage::class,
                ],
                'envelope' => Envelope::wrap(new Message()),
                'expectedEnvelope' => Envelope::wrap(new Message()),
            ],
            'has matching message class name' => [
                'messageClassNames' => [
                    RetryableMessage::class,
                ],
                'envelope' => Envelope::wrap(new RetryableMessage()),
                'expectedEnvelope' => Envelope::wrap(
                    new RetryableMessage(),
                    [
                        new NonDispatchableStamp(IgnoredMessageMiddleware::REASON),
                    ]
                ),
            ],
        ];
    }
}

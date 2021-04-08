<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Tests\Unit\Middleware;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\DelayedMessageMiddleware;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\MessageOne;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\MessageTwo;

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
                'envelope' => Envelope::wrap(new MessageOne()),
                'expectedEnvelope' => Envelope::wrap(new MessageOne()),
            ],
            'no relevant delays' => [
                'delays' => [
                    MessageTwo::class => 1000,
                ],
                'envelope' => Envelope::wrap(new MessageOne()),
                'expectedEnvelope' => Envelope::wrap(new MessageOne()),
            ],
            'has relevant delays' => [
                'delays' => [
                    MessageTwo::class => 1000,
                ],
                'envelope' => Envelope::wrap(new MessageTwo()),
                'expectedEnvelope' => Envelope::wrap(
                    new MessageTwo(),
                    [
                        new DelayStamp(1000)
                    ]
                ),
            ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Tests\Unit\Middleware\DelayedMessage;

use PHPUnit\Framework\TestCase;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\DelayedMessage\ExponentialBackoffStrategy;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\Message;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\RetryableMessage;

class ExponentialBackoffStrategyTest extends TestCase
{
    /**
     * @dataProvider getDelayDataProvider
     */
    public function testGetDelay(int $window, object $message, int $expectedDelay): void
    {
        $strategy = new ExponentialBackoffStrategy($window);

        self::assertSame($expectedDelay, $strategy->getDelay($message));
    }

    /**
     * @return array[]
     */
    public function getDelayDataProvider(): array
    {
        return [
            'zero window, non-retryable message' => [
                'window' => 0,
                'message' => new Message(),
                'expectedDelay' => 0,
            ],
            'non-zero window, non-retryable message' => [
                'window' => 1000,
                'message' => new Message(),
                'expectedDelay' => 0,
            ],
            'zero window, retryable message, retry count 0' => [
                'window' => 0,
                'message' => new RetryableMessage(),
                'expectedDelay' => 0,
            ],
            'zero window, retryable message, retry count 1' => [
                'window' => 0,
                'message' => new RetryableMessage(1),
                'expectedDelay' => 0,
            ],
            'zero window, retryable message, retry count 2' => [
                'window' => 0,
                'message' => new RetryableMessage(2),
                'expectedDelay' => 0,
            ],
            'non-zero window, retryable message, retry count 0' => [
                'window' => 1000,
                'message' => new RetryableMessage(),
                'expectedDelay' => 0,
            ],
            'non-zero window, retryable message, retry count 1' => [
                'window' => 1000,
                'message' => new RetryableMessage(1),
                'expectedDelay' => 1000,
            ],
            'non-zero window, retryable message, retry count 2' => [
                'window' => 1000,
                'message' => new RetryableMessage(2),
                'expectedDelay' => 3000,
            ],
            'non-zero window, retryable message, delay > PHP_INT_MAX' => [
                'window' => 1000,
                'message' => new RetryableMessage(100),
                'expectedDelay' => PHP_INT_MAX,
            ],
        ];
    }
}

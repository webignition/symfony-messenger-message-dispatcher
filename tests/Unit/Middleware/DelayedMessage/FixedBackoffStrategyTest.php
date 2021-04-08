<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Tests\Unit\Middleware\DelayedMessage;

use PHPUnit\Framework\TestCase;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\DelayedMessage\FixedBackoffStrategy;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Model\Message;

class FixedBackoffStrategyTest extends TestCase
{
    /**
     * @dataProvider getDelayDataProvider
     */
    public function testGetDelay(int $delay, int $expectedDelay): void
    {
        $strategy = new FixedBackoffStrategy($delay);

        self::assertSame($expectedDelay, $strategy->getDelay(new Message()));
    }

    /**
     * @return array[]
     */
    public function getDelayDataProvider(): array
    {
        return [
            'zero delay' => [
                'delay' => 0,
                'expectedDelay' => 0,
            ],
            'negative delay' => [
                'delay' => -1,
                'expectedDelay' => -1,
            ],
            'positive delay' => [
                'delay' => 123,
                'expectedDelay' => 123,
            ],
        ];
    }
}

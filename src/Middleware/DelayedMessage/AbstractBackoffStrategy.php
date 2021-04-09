<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Middleware\DelayedMessage;

abstract class AbstractBackoffStrategy implements BackoffStrategyInterface
{
    protected function normalizeDelay(int | float $delay): int
    {
        return (int) min($delay, PHP_INT_MAX);
    }
}

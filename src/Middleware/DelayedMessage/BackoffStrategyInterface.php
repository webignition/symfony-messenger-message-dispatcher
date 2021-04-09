<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Middleware\DelayedMessage;

interface BackoffStrategyInterface
{
    /**
     * @return int Delay in milliseconds, between 0 and PHP_INT_MAX
     */
    public function getDelay(object $message): int;
}

<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Middleware\DelayedMessage;

interface BackoffStrategyInterface
{
    /**
     * @return int Delay in milliseconds
     */
    public function getDelay(object $message): int;
}

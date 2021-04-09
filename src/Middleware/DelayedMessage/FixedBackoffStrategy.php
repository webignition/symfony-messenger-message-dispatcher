<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Middleware\DelayedMessage;

class FixedBackoffStrategy extends AbstractBackoffStrategy
{
    public function __construct(
        private int $delayInMilliseconds,
    ) {
    }

    public function getDelay(object $message): int
    {
        return $this->normalizeDelay($this->delayInMilliseconds);
    }
}

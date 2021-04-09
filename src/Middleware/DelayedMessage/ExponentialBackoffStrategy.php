<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Middleware\DelayedMessage;

use webignition\SymfonyMessengerMessageDispatcher\Message\RetryableMessageInterface;

class ExponentialBackoffStrategy extends AbstractBackoffStrategy
{
    public function __construct(private int $window = 1000)
    {
    }

    public function getDelay(object $message): int
    {
        $retryCount = $message instanceof RetryableMessageInterface
            ? $message->getRetryCount()
            : 0;

        $factor = (2 ** $retryCount) - 1;

        return $this->normalizeDelay($factor * $this->window);
    }
}

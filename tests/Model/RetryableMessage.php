<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Tests\Model;

use webignition\SymfonyMessengerMessageDispatcher\Message\RetryableMessageInterface;

class RetryableMessage implements RetryableMessageInterface
{
    public function __construct(
        private int $retryCount = 0,
    ) {
    }

    public function getRetryCount(): int
    {
        return $this->retryCount;
    }
}

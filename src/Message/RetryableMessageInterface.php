<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Message;

interface RetryableMessageInterface
{
    public function getRetryCount(): int;
}

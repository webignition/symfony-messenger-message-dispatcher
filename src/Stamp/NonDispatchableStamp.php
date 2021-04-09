<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Stamp;

class NonDispatchableStamp implements NonDispatchableStampInterface
{
    public function __construct(
        private string $reason
    ) {
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}

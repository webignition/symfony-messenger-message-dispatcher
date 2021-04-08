<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Middleware\Result;

use Symfony\Component\Messenger\Envelope;

class Result implements ResultInterface
{
    private bool $isDispatchable = true;
    private string $nonDispatchableReason = '';

    private function __construct(
        private Envelope $envelope,
    ) {
    }

    public static function createDispatchable(Envelope $envelope): self
    {
        return new Result($envelope);
    }

    public static function createNonDispatchable(Envelope $envelope, string $reason): self
    {
        $result = new Result($envelope);
        $result->isDispatchable = false;
        $result->nonDispatchableReason = $reason;

        return $result;
    }

    public function getEnvelope(): Envelope
    {
        return $this->envelope;
    }

    public function isDispatchable(): bool
    {
        return $this->isDispatchable;
    }

    public function getNonDispatchableReason(): string
    {
        return $this->nonDispatchableReason;
    }
}

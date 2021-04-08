<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Middleware\Result;

use Symfony\Component\Messenger\Envelope;

interface ResultInterface
{
    public function getEnvelope(): Envelope;
    public function isDispatchable(): bool;
    public function getNonDispatchableReason(): string;
}

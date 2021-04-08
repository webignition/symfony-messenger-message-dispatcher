<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Tests\Services;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class MessageBus implements MessageBusInterface
{
    /**
     * @var array<mixed>
     */
    private array $stack = [];


    public function dispatch($message, array $stamps = []): Envelope
    {
        $envelope = Envelope::wrap($message, $stamps);

        $this->stack[] = $envelope;

        return $envelope;
    }

    /**
     * @return array<mixed>
     */
    public function getStack(): array
    {
        return $this->stack;
    }
}

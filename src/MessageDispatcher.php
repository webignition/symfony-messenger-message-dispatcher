<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;

class MessageDispatcher implements MessageBusInterface
{
    /**
     * @var MiddlewareInterface[]
     */
    private array $middleware;

    /**
     * @param MiddlewareInterface[] $middleware
     */
    public function __construct(
        private MessageBusInterface $messageBus,
        array $middleware = [],
    ) {
        $this->middleware = array_filter($middleware, function ($value) {
            return $value instanceof MiddlewareInterface;
        });
    }

    /**
     * @param object|Envelope $message
     * @param StampInterface[] $stamps
     *
     * @throws NonDispatchableMessageExceptionInterface
     */
    public function dispatch($message, array $stamps = []): Envelope
    {
        $envelope = Envelope::wrap($message, $stamps);

        foreach ($this->middleware as $middleware) {
            $envelope = ($middleware)($envelope);
        }

        return $this->messageBus->dispatch($envelope);
    }
}

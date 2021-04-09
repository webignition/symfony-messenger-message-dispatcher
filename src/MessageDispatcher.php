<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\MiddlewareInterface;
use webignition\SymfonyMessengerMessageDispatcher\Stamp\NonDispatchableStampInterface;

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
     */
    public function dispatch($message, array $stamps = []): Envelope
    {
        $envelope = Envelope::wrap($message, $stamps);
        $isDispatchable = true;

        foreach ($this->middleware as $middleware) {
            if ($isDispatchable) {
                $envelope = ($middleware)($envelope);
                $isDispatchable = self::isDispatchable($envelope);
            }
        }

        return $isDispatchable
            ? $this->messageBus->dispatch($envelope)
            : $envelope;
    }

    public static function isDispatchable(Envelope $envelope): bool
    {
        $stamps = $envelope->all();

        foreach ($stamps as $value) {
            if ($value instanceof NonDispatchableStampInterface) {
                return false;
            }

            if (is_array($value)) {
                foreach ($value as $stamp) {
                    if ($stamp instanceof NonDispatchableStampInterface) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}

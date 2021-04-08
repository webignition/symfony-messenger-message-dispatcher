<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Middleware\DelayedMessage;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\MiddlewareInterface;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\Result\Result;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\Result\ResultInterface;

class DelayedMessageMiddleware implements MiddlewareInterface
{
    public const WILDCARD = '*';

    /**
     * @param array<string, int> $delaysInMilliseconds
     */
    public function __construct(
        private array $delaysInMilliseconds = [],
    ) {
    }

    public function __invoke(Envelope $envelope): ResultInterface
    {
        $delay = $this->findDelay($envelope);

        if (is_int($delay) && $delay > 0) {
            $envelope = $envelope
                ->withoutStampsOfType(DelayStamp::class)
                ->with(new DelayStamp($delay));
        }

        return Result::createDispatchable($envelope);
    }

    private function findDelay(Envelope $envelope): ?int
    {
        $message = $envelope->getMessage();
        $identifiers = [$message::class, self::WILDCARD];

        foreach ($identifiers as $identifier) {
            if (array_key_exists($identifier, $this->delaysInMilliseconds)) {
                return $this->delaysInMilliseconds[$identifier];
            }
        }

        return null;
    }
}

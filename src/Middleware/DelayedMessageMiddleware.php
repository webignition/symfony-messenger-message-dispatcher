<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\Result\Result;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\Result\ResultInterface;

class DelayedMessageMiddleware implements MiddlewareInterface
{
    /**
     * @param array<class-string, int> $delaysInMilliseconds
     */
    public function __construct(
        private array $delaysInMilliseconds = [],
    ) {
    }

    public function __invoke(Envelope $envelope): ResultInterface
    {
        $message = $envelope->getMessage();
        $delay = $this->delaysInMilliseconds[$message::class] ?? 0;

        if ($delay > 0) {
            $envelope = $envelope
                ->withoutStampsOfType(DelayStamp::class)
                ->with(new DelayStamp($delay));
        }

        return Result::createDispatchable($envelope);
    }
}

<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Middleware;

use Symfony\Component\Messenger\Envelope;
use webignition\SymfonyMessengerMessageDispatcher\Message\RetryableMessageInterface;
use webignition\SymfonyMessengerMessageDispatcher\Stamp\NonDispatchableStamp;

class RetryByLimitMiddleware implements MiddlewareInterface
{
    public const REASON = 'retry limit reached';

    /**
     * @param array<class-string, int> $retryLimits
     */
    public function __construct(
        private array $retryLimits = [],
    ) {
    }

    public function __invoke(Envelope $envelope): Envelope
    {
        $message = $envelope->getMessage();
        if (!$message instanceof RetryableMessageInterface) {
            return $envelope;
        }

        $retryLimit = $this->retryLimits[$message::class] ?? 0;

        if ($message->getRetryCount() > $retryLimit) {
            $envelope = $envelope
                ->withoutStampsOfType(NonDispatchableStamp::class)
                ->with(new NonDispatchableStamp(self::REASON));
        }

        return $envelope;
    }
}

<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Middleware;

use Symfony\Component\Messenger\Envelope;
use webignition\SymfonyMessengerMessageDispatcher\Message\RetryableMessageInterface;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\Result\Result;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\Result\ResultInterface;

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

    public function __invoke(Envelope $envelope): ResultInterface
    {
        $message = $envelope->getMessage();
        if (!$message instanceof RetryableMessageInterface) {
            return Result::createDispatchable($envelope);
        }

        $retryLimit = $this->retryLimits[$message::class] ?? 0;

        if ($message->getRetryCount() > $retryLimit) {
            return Result::createNonDispatchable($envelope, self::REASON);
        }

        return Result::createDispatchable($envelope);
    }
}

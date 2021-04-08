<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Middleware;

use Symfony\Component\Messenger\Envelope;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\Result\Result;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\Result\ResultInterface;

class IgnoredMessageMiddleware implements MiddlewareInterface
{
    public const REASON = 'ignored';

    /**
     * @param array<class-string> $messageClassNames
     */
    public function __construct(
        private array $messageClassNames = [],
    ) {
    }

    public function __invoke(Envelope $envelope): ResultInterface
    {
        $message = $envelope->getMessage();

        return in_array($message::class, $this->messageClassNames)
            ? Result::createNonDispatchable($envelope, self::REASON)
            : Result::createDispatchable($envelope);
    }
}

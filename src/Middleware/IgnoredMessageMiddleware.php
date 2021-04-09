<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Middleware;

use Symfony\Component\Messenger\Envelope;
use webignition\SymfonyMessengerMessageDispatcher\Stamp\NonDispatchableStamp;

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

    public function __invoke(Envelope $envelope): Envelope
    {
        $message = $envelope->getMessage();

        if (in_array($message::class, $this->messageClassNames)) {
            $envelope = $envelope
                ->withoutStampsOfType(NonDispatchableStamp::class)
                ->with(new NonDispatchableStamp(self::REASON));
        }

        return $envelope;
    }
}

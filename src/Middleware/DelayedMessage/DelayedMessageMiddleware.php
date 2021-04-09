<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Middleware\DelayedMessage;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\MiddlewareInterface;

class DelayedMessageMiddleware implements MiddlewareInterface
{
    public const WILDCARD = '*';

    /**
     * @var BackoffStrategyInterface[]
     */
    private array $backoffStrategies = [];

    /**
     * @param array<string, BackoffStrategyInterface> $backoffStrategies
     */
    public function __construct(
        array $backoffStrategies = [],
    ) {
        foreach ($backoffStrategies as $key => $value) {
            if ($value instanceof BackoffStrategyInterface) {
                $this->backoffStrategies[$key] = $value;
            }
        }
    }

    public function __invoke(Envelope $envelope): Envelope
    {
        $message = $envelope->getMessage();

        $backoffStrategy = $this->findBackoffStrategy($envelope->getMessage());
        $delay = $backoffStrategy instanceof BackoffStrategyInterface
            ? $backoffStrategy->getDelay($message)
            : 0;

        if ($delay > 0) {
            $envelope = $envelope
                ->withoutStampsOfType(DelayStamp::class)
                ->with(new DelayStamp($delay));
        }

        return $envelope;
    }

    private function findBackoffStrategy(object $message): ?BackoffStrategyInterface
    {
        $identifiers = [$message::class, self::WILDCARD];

        foreach ($identifiers as $identifier) {
            if (array_key_exists($identifier, $this->backoffStrategies)) {
                return $this->backoffStrategies[$identifier];
            }
        }

        return null;
    }
}

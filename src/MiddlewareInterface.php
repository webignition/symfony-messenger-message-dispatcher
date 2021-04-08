<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher;

use Symfony\Component\Messenger\Envelope;

interface MiddlewareInterface
{
    /**
     * @throws NonDispatchableMessageExceptionInterface
     */
    public function __invoke(Envelope $envelope): Envelope;
}

<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Middleware;

use Symfony\Component\Messenger\Envelope;
use webignition\SymfonyMessengerMessageDispatcher\Exception\NonDispatchableMessageExceptionInterface;

interface MiddlewareInterface
{
    /**
     * @throws NonDispatchableMessageExceptionInterface
     */
    public function __invoke(Envelope $envelope): Envelope;
}

<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Middleware;

use Symfony\Component\Messenger\Envelope;
use webignition\SymfonyMessengerMessageDispatcher\Middleware\Result\ResultInterface;

interface MiddlewareInterface
{
    public function __invoke(Envelope $envelope): ResultInterface;
}

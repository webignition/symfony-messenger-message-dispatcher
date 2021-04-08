<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher;

interface NonDispatchableMessageExceptionInterface extends \Throwable
{
    public function getMessageObject(): object;
}

<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Exception;

interface NonDispatchableMessageExceptionInterface extends \Throwable
{
    public function getMessageObject(): object;
}

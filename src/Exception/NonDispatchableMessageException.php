<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Exception;

class NonDispatchableMessageException extends \Exception implements NonDispatchableMessageExceptionInterface
{
    public function __construct(
        private object $messageObject
    ) {
        parent::__construct();
    }


    public function getMessageObject(): object
    {
        return $this->messageObject;
    }
}

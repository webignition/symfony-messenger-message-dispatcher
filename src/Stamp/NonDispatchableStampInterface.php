<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Stamp;

use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;

interface NonDispatchableStampInterface extends NonSendableStampInterface
{
    public function getReason(): string;
}

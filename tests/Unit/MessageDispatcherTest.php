<?php

declare(strict_types=1);

namespace webignition\SymfonyMessengerMessageDispatcher\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use webignition\SymfonyMessengerMessageDispatcher\MessageDispatcher;
use webignition\SymfonyMessengerMessageDispatcher\Tests\Services\MessageBus;

class MessageDispatcherTest extends TestCase
{
    public function testDispatch(): void
    {
        $message = (object) [
            'key' => 'value',
        ];

        $messageBus = new MessageBus();

        $messageDispatcher = new MessageDispatcher($messageBus);

        $messageDispatcher->dispatch($message);

        $messageBusStack = $messageBus->getStack();

        self::assertEquals(Envelope::wrap($message), $messageBusStack[0]);
    }
}

<?php

namespace App\Infrastructure\Bus;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class CommandBus
{
    public function __construct(private readonly MessageBusInterface $commandBus)
    {
    }

    /**
     * @return mixed The handler returned value
     * @throws HandlerFailedException
     */
    public function command(object $command, array $stamps = []): mixed
    {
        $envelope = $this->commandBus->dispatch(new Envelope($command, $stamps));
        /** @var HandledStamp[] $handledStamps */
        $handledStamps = $envelope->all(HandledStamp::class);

        if (!$handledStamps) {
            return null;
        }
        if (\count($handledStamps) > 1) {
            throw new LogicException(
                sprintf('Message of type "%s" was handled multiple times', get_debug_type($envelope->getMessage()))
            );
        }
        return $handledStamps[0]->getResult();
    }
}

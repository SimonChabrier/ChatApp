<?php

namespace App\MessageHandler;

use App\Message\Mercure;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class MercureHandler implements MessageHandlerInterface
{   
    private $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }

    public function __invoke(Mercure $message)
    {
        $update = new Update(
            $message->getTopic(),
            $message->getData()
        );

        $this->hub->publish($update);
    }
}

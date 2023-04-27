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
        // on crée un objet Update qui contient le nom du topic et les données à publier
        $update = new Update(
            $message->getTopic(),
            $message->getData()
        );

        // on publie l'objet Update dans le hub qui va ensuite envoyer les données aux clients abonnés
        $this->hub->publish($update);
    }
}

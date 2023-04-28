<?php

namespace App\MessageHandler;

use App\Message\MercureMessage;
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

    public function __invoke(MercureMessage $message)
    {   
        // on crée un objet JSON qui contient les données à publier et retourner en JSON au frontend tout de suite après pour l'afficher.
        $data = json_encode([
            'message' => $message->getData(),
            'author' => $message->getUsername()
        ]);

        // on crée un objet Update qui contient le nom du topic et les données à publier
        $update = new Update(
            $message->getTopic(),
            $data
        );

        // on publie l'objet Update dans le hub qui va ensuite envoyer les données aux clients abonnés
        $this->hub->publish($update);
    }
}

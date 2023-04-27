<?php

namespace App\MessageHandler;

use App\Message\Chat;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ChatHandler implements MessageHandlerInterface
{   
    // TODO : à décommenter pour sauvegarder les messages en BDD
    // private $message;
    // private $manager;

    // public function __construct(Messsage $rmessage, EntityManagerInterface $manager)
    // {
    //     $this->message = $rmessage;
    //     $this->manager = $manager;
    // }

     public function __invoke(Chat $message)
    {
        // // on crée un objet Message et on le remplit avec les données du formulaire
        // $message = new Message();
        // $message->setMessage($message->getMessage());
        // $message->setTopic($message->getTopic());
        // // ici il faut récupèrer le user connecté pour le mettre dans le champ author
        // $message->setAuthor($message->getUserName());

        // // on sauvegarde le message en BDD
        // $this->manager->persist($message);
        // $this->manager->flush();

    }
}

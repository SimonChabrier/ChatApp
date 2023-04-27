<?php

namespace App\MessageHandler;

use App\Entity\Message;
use App\Entity\Channel;
use App\Entity\User;

use App\Message\Chat;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ChatHandler implements MessageHandlerInterface
{   
    // TODO : à décommenter pour sauvegarder les messages en BDD
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

     public function __invoke(Chat $chat)
    {   
        $channel = $this->manager->getRepository(Channel::class)->findOneBy(['id' => $chat->getChannelId()]);
        $user = $this->manager->getRepository(User::class)->findOneBy(['id' => $chat->getAuthorId()]);

        $message = new Message();
        $message->setContent($chat->getMessage())
            ->setChannel($channel)
            ->setAuthor($user);

        // on sauvegarde le message en BDD
        $this->manager->persist($message);
        $this->manager->flush();

    }
}

<?php

namespace App\MessageHandler;

use App\Entity\User;
use App\Entity\Message;
use App\Entity\Conversation;

use App\Message\PrivateMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PrivateHandler implements MessageHandlerInterface
{   
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

     public function __invoke(PrivateMessage $privateMessage)
    {   
        $conversation = $this->manager->getRepository(Conversation::class)->findOneBy(['id' => $privateMessage->getPrivateConversationId()]);
        $user = $this->manager->getRepository(User::class)->findOneBy(['id' => $privateMessage->getAuthorId()]);

        $message = new Message();
        $message->setContent($privateMessage->getPrivateMessage())
            ->setConversation($conversation)
            ->setAuthor($user);

        // on sauvegarde le message en BDD
        $this->manager->persist($message);
        $this->manager->flush();

    }
}

<?php

namespace App\Services;

use App\Entity\Conversation;
use App\Entity\User;
use App\Repository\ConversationRepository;

class isConversationExist
{
    private $conversationRepository;

    public function __construct(ConversationRepository $conversationRepository)
    {
        $this->conversationRepository = $conversationRepository;
    }

    /**
     * Vérifie si une conversation existe déjà entre les utilisateurs exacts
     * et retourne la conversation si elle existe ou null si elle n'existe pas
     * en utilisant une requête personnalisée dans le repository ConversationRepository
     *
     * @param array $participants
     * @param User $currentUser
     * @return Conversation|null
     */
    public function checkUsersConversations(array $participants, User $currentUser): ?Conversation
    {
        $conversation = $this->conversationRepository->findByParticipants($participants, $currentUser);

        if ($conversation && $conversation->getUsers()->contains($currentUser)) {
            return $conversation;
        }

        return null;
    }
}

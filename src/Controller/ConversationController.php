<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\User;
use App\Services\isConversationExist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class ConversationController extends AbstractController
{
    /**
     * @Route("/conversation", name="app_conversation", methods={"POST"})
     */
    public function createConversation(EntityManagerInterface $manager,isConversationExist $isConversationExist, Request $request, SerializerInterface $serializer): Response
    {   
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse([
                'error' => 'Vous devez être connecté pour créer une conversation'
            ], 401);
        } 

        // on récupère le tableau des id des participants envoyé par JS
        $requestData = json_decode($request->getContent(), true);
        $participantIds = $requestData['participants'];

        if (!$participantIds) {
            return new JsonResponse([
                'error' => 'Vous devez sélectionner au moins un participant'
            ], 400);
        }
        
        // on récupère les utilisateurs correspondants aux ids des participants du tableau envoyé par JS
        $participants = [];
        
            foreach ($participantIds as $participantId) {
                $participant = $manager->getRepository(User::class)->findOneBy(['id' => (int) $participantId]);
                if ($participant) { 
                    $participants[] = $participant;
                }
            }

            if (count($participants) !== count($participantIds)) {
                    return new JsonResponse([
                        'error' => 'Certains participants n\'existent pas'
                    ], 400);
            };
            
        // vérifier si la conversation existe déjà avec le service isConversationExist 
        // qui retourne la conversation si elle existe ou null si elle n'existe pas
        
        $currentConversation = $isConversationExist->checkUsersConversations($participants, $user);

            if ($currentConversation !== null) {
                // Si la conversation existe déjà, on retourne la conversation existante en json
                $jsonContent = $serializer->serialize($currentConversation, 'json', ['groups' => 'private_conversation']);

                return new JsonResponse([
                    'conversation' => json_decode($jsonContent),
                    'status' => 'conversation existante en cours...'
                ], 200);

            } else {
                // Crée une nouvelle conversation
                $conversation = new Conversation();
                $conversation->addUser($user);
                foreach ($participants as $participant) {
                    $conversation->addUser($participant);
                }

                // Utilisation de wrapInTransaction de Doctrine pour encapsuler la persistance et le flush dans une transaction unique
                // Cela permet d'éviter les conflits de transactions, où plusieurs appels concurrents tentent d'accéder simultanément 
                // à la même base de données, ce qui peut entraîner des erreurs et des transactions annulées.
                $manager->wrapInTransaction(function ($manager) use ($conversation) {
                    $manager->persist($conversation);
                    $manager->flush();
                });

                // sérialize la nouvelle conversation pour l'initialiser après sa création et la retourner.
                $jsonContent = $serializer->serialize($conversation, 'json', ['groups' => 'private_conversation']);
            
                return new JsonResponse([
                    'conversation' => json_decode($jsonContent),
                    'status' => 'Nouvelle conversation créée'
                ], 200);
            }
    }


}

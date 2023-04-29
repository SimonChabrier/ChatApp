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

class ConversationController extends AbstractController
{
    /**
     * @Route("/conversation", name="app_conversation", methods={"POST"})
     */
    public function createConversation(EntityManagerInterface $manager,isConversationExist $isConversationExist, Request $request): Response
    {   
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse([
                'error' => 'Vous devez être connecté pour créer une conversation'
            ], 401);
        } 

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

                return new JsonResponse([
                    // retourner la conversation existante en json pour l'afficher dans le front dynamiquement en pop-up bas de page
                    'conversation' => $currentConversation,
                    'status' => 'conversation existante en cours...'
                ], 200);

            } else {
                // Crée une nouvelle conversation
                $conversation = new Conversation();
                $conversation->addUser($user);
                foreach ($participants as $participant) {
                    $conversation->addUser($participant);
                }
                // $manager->persist($conversation);
                // $manager->flush();

                // Utilisation de wrapInTransaction de Doctrine pour encapsuler la persistance et le flush dans une transaction unique
                // Cela permet d'éviter les conflits de transactions, où plusieurs appels concurrents tentent d'accéder simultanément 
                // à la même base de données, ce qui peut entraîner des erreurs et des transactions annulées.
                $manager->wrapInTransaction(function ($manager) use ($conversation) {
                    $manager->persist($conversation);
                    $manager->flush();
                });
            
                return new JsonResponse([
                    'conversation' => $conversation,
                    'status' => 'Nouvelle conversation créée'
                ], 200);
            }
    }

}

<?php

namespace App\Controller;

use App\Message\ChatMessage;
use App\Message\MercureMessage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LIveMessageController extends AbstractController
{   

    /**
     * Route qui récupère les données du formulaire les dipatche dans le bus
     * Le bus les envoie ensuite au handler (MercureHandler) qui les publie dans le hub en syncrone.
     * et dans le handler (ChatHandler) qui les stocke en BDD en asynchrone.
     * 
     * @Route("/publish", name="app_publish")
     */
    public function getMessage(
            MessageBusInterface $bus,
            Request $request
            ): Response
    {   
        $data = json_decode(
            $request->getContent(), true // getContent() récupère le body de la requête
        );
        
        $user = $this->getUser();

        // Ici on dispatche en synchrone en interne car le hub Meruure est asynchrone par défaut de son côté
        // donc c'est lui qui va gérer l'envoi des données aux clients sans bloquer le serveur de cette app.
        // et qu'on a besoin de récupérer la réponse pour l'afficher dans la vue tout de suite.
        // Pas besoin de lancer de worker pour le bus en synchrone et pas besoin de table dans la BDD pour les messages.
        $bus->dispatch(new MercureMessage(
            $data['topic'],
            htmlspecialchars($data['message']),  
            $user->getUserIdentifier())
        );

        // maintenant on dispatche en asynchrone pour gèrer le stockage des messages en BDD
        $bus->dispatch(new ChatMessage(
            htmlspecialchars($data['message']), 
            (int) $data['channel_id'], 
            (int) $data['author_id'])
        );

        return new JsonResponse([
            'topic' => $data['topic'],
            'message' => $data['message'],
            'channel_id' => (int) $data['channel_id'],
            'author' => $data['author'],
            'author_id' => (int) $data['author_id'],
        ]);
    }
}
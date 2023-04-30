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
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class ChatController extends AbstractController
{   

    // dans cette classe on utilise messenger pour dispatcher les messages dans le bus
    // on a un diptach synchrone (MercureMessage & MercureHandler) pour le hub mercure
    // et un dispatch asynchrone (ChatMessage & ChatHandler) pour le handler qui stocke les messages en BDD

    /**
     * Route qui récupère les données du formulaire les dipatche dans le bus
     * Le bus les envoie ensuite au handler (MercureHandler) qui les publie dans le hub en syncrone.
     * et dans le handler (ChatHandler) qui les stocke en BDD en asynchrone.
     * 
     * @Route("/publish", name="app_publish")
     */
    public function getPublicChannelMessage(
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

        // notification du channel en cas de nouveau message
        // dispatch en synchrone car on a besoin de la réponse pour l'afficher dans la vue tout de suite
        $bus->dispatch(new MercureMessage(
            'channel/' . (int) $data['channel_id'],
            json_encode([
                // message non utilisé pour l'instant
                'notification_message' => 'Nouveau message dans le channel ' . $data['topic']
            ]),
            $user->getUserIdentifier())
        );
        
        // on se retourne les infos pour les exploiter au besoin dans la vue
        // pour le moment on les console.log dans le fichier js juste pour vérifier
        // parce que tout part et revient par le hub mercure et ce sont les données du hub qui sont affichées dans la vue.
        return new JsonResponse([
            'topic' => $data['topic'],
            'message' => $data['message'],
            'channel_id' => (int) $data['channel_id'],
            'author' => $data['author'],
            'author_id' => (int) $data['author_id'],
            'count' => 1,
        ]);
    }

    /**
     * Route qui récupère les données des conversations privées du formulaire les dipatche dans le bus
     * 
     * @Route("/publish/private", name="app_publish_private")
     */
    public function getPrivateChannelMessage(
            Request $request,
            HubInterface $hub
            ): Response
    {
    $data = json_decode(
        $request->getContent(),
        true // getContent() récupère le body de la requête
    );

    // Ici on dispatche en synchrone en interne car le hub Mercure est asynchrone par défaut de son côté
    // donc c'est lui qui va gérer l'envoi des données aux clients sans bloquer le serveur de cette app.
    // et qu'on a besoin de récupérer la réponse pour l'afficher dans la vue tout de suite.
    // Pas besoin de lancer de worker pour le bus en synchrone et pas besoin de table dans la BDD pour les messages.
    $update = new Update(
        $data['topic'],
        json_encode([
            //'topic' => $data['topic'],
            'message' => htmlspecialchars($data['message']),
            'conversation_id' => (int) $data['conversation_id'],
            //'author' => $data['author'],
            'author_id' => (int) $data['author_id'],
        ])
    );

        $hub->publish($update);

        return new JsonResponse(['message' => 'ok']);

    }
                
}

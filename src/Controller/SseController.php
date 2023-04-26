<?php

namespace App\Controller;

use App\Message\Mercure;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SseController extends AbstractController
{   

    /**
     * Route qui récupère les données du formulaire les dipatche dans le bus
     * Le bus les envoie ensuite au handler (MercureHandler) qui les publie dans le hub.
     * 
     * @Route("/publish", name="app_publish")
     */
    public function index(
            MessageBusInterface $bus,
            Request $request
            ): Response
    {   
        $data = json_decode(
            $request->getContent(), true // getContent() récupère le body de la requête
        );
        
        // Ici on dispatche en synchrone en interne car le hub Meruure est asynchrone par défaut de son côté
        // donc c'est lui qui va gérer l'envoi des données aux clients sans bloquer le serveur de cette app.
        // et qu'on a besoin de récupérer la réponse pour l'afficher dans la vue tout de suite.
        $bus->dispatch(new Mercure('montopic', $data));

        return new JsonResponse([
            'retour' => $data
        ]);
    }
}

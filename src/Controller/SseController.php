<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class SseController extends AbstractController
{
    /**
     * @Route("/publish", name="app_publish")
     */
    public function index(HubInterface $hub, Request $request): Response
    {   
        $data = json_decode($request->getContent(), true);

        $update = new Update(
            'montopic',
            $data
        );

        $hub->publish($update);

        return new JsonResponse([
            'retour' => $data
        ]);
    }
}

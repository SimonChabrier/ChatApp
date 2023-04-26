<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class SseController extends AbstractController
{
    /**
     * @Route("/publish", name="app_publish")
     */
    public function index(HubInterface $hub): Response
    {
        $update = new Update(
            'montopic',
            json_encode(['coucou'])
        );

        $hub->publish($update);

        return new Response('ok!');
    }
}

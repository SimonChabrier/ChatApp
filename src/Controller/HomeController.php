<?php

namespace App\Controller;

use App\Repository\ChannelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(ChannelRepository $channelRepository): Response
    {   
        return $this->render('home/index.html.twig', [
            'channels' => $channelRepository->findAll(),
        ]);
    }
}

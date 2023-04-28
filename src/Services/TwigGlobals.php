<?php

namespace App\Services;

use App\Entity\Channel;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TwigGlobals
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    // retourne toutes les cannaux pour sidebar
    // déclaré dans twig comme un service injecté dans une variable 'channels' rendue globale..(voir config/twig.yaml)
    // la méthode est ensuite appellée dans twig comme ceci: {% for channel in channels.getChannels() %}  ...
    
    public function getGlobals()
    {   
        $all_channels = $this->em->getRepository(Channel::class)->findAll();
        $online_users = $this->em->getRepository(User::class)->findBy(['online' => 1]);

        return [
            'all_channels' => $all_channels,
            'online_users' => $online_users
        ];
    }

}
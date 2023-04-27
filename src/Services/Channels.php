<?php

namespace App\Services;

use App\Entity\Channel;
use Doctrine\ORM\EntityManagerInterface;

class Channels
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    // retourne toutes les cannaux pour sidebar
    // déclaré dans twig comme un service injecté dans une variable 'channels' rendue globale..(voir config/twig.yaml)
    // la méthode est ensuite appellée dans twig comme ceci: {% for channel in channels.getChannels() %}  ...
    
    public function getAllChannels()
    {   
        return $this->em->getRepository(Channel::class)->findAll();
    }
}
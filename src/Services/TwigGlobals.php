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
    // déclaré dans twig comme un service injecté dans une variable 'twig_globals' rendue globale..(voir config/twig.yaml)
    // la méthode est ensuite appellée dans twig comme ceci: {% for channel in wig_globals.getGlobals()['all_channels'] %}  ...
    // on se positionne donc sur chaque clé pour boucler sur les valeurs de chaque clé...ça évite de faire X services pour chaque 
    // variable globale à rendre disponible dans twig.

    public function getGlobals()
    {   
        return [
            'all_channels' => $this->em->getRepository(Channel::class)->findAll(),
            'online_users' => $this->em->getRepository(User::class)->findBy(['online' => 1]),
        ];
    }

}
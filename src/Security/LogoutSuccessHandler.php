<?php 

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;


// flush user status on logout (set online to 0)

class LogoutSuccessHandler extends AbstractController implements LogoutSuccessHandlerInterface
{
    private $userRepository;
    private $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }
    public function onLogoutSuccess(Request $request): Response
    {   
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = $this->getUser();
        $user->setOnline(0);
        
        $this->em->persist($user);
        $this->em->flush();

        return $this->redirectToRoute('app_home');

    }
}
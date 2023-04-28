<?php 

namespace App\Security;

use App\Entity\User;

use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;

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
    private EntityManagerInterface $em;
    private HubInterface $hub;

    public function __construct(EntityManagerInterface $em, HubInterface $hub)
    {
        $this->em = $em;
        $this->hub = $hub;
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

        // on crée un objet Update qui contient le nom du topic et les données à publier
        $update = new Update(
            'user_disconnected',
            json_encode([
                'username' => $user->getUserIdentifier(), 
                'status' => "offline", 
                'user_id' => $user->getId()])
            );
        // on publie l'objet Update dans le hub qui va ensuite envoyer les données aux clients abonnés
        $this->hub->publish($update);

        return $this->redirectToRoute('app_home');

    }
}
<?php

namespace App\Security;

use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;
    private EntityManagerInterface $em;
    private HubInterface $hub;

    public function __construct(UrlGeneratorInterface $urlGenerator, EntityManagerInterface $em, HubInterface $hub)
    {
        $this->urlGenerator = $urlGenerator;
        $this->em = $em;
        $this->hub = $hub;
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('username', '');

        $request->getSession()->set(Security::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {

            // get the user by authenticating token
            $user = $token->getUser();  
            // set user online status to 1
            $user->setOnline(1);
            // get the current value og login_count and add +1
            $user->setLoginCount($user->getLoginCount() + 1);
            // set the last login date to now
            $user->setLastConnection(new \DateTime());
            // save the changes

            $this->em->persist($user);
            $this->em->flush();

            // set an update to the hub to notify the user is online
            $update = new Update(
                'user_connected',
                json_encode([
                    'username' => $user->getUserIdentifier(), 
                    'status' => "online", 
                    'user_id' => $user->getId()])
                );
            // publish user status update to the hub to be sent to the client
            // the client will then see update the user status in the user list
            $this->hub->publish($update);

            return new RedirectResponse($targetPath);
        }
        

        return new RedirectResponse($this->urlGenerator->generate('app_home'), Response::HTTP_SEE_OTHER);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}

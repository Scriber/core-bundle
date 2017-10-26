<?php
namespace Scriber\Bundle\CoreBundle\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Scriber\Bundle\CoreBundle\User\UserManager;

class AuthenticationSuccessEventListener
{
    /**
     * @var UserManager
     */
    private $manager;

    /**
     * @param UserManager $manager
     */
    public function __construct(UserManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $securityUser = $event->getUser();

        $user = $this->manager->getUser($securityUser->getUsername());

        $data = $event->getData();
        $data['user'] = [
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'roles' => $user->getRoles()
        ];

        $event->setData($data);
    }
}

<?php
namespace Scriber\Bundle\CoreBundle\Controller\Admin\MyAccount;

use Scriber\Bundle\CoreBundle\Http\JsonResponseData;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class IndexController
{
    /**
     * @var UserManager
     */
    private $manager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param UserManager $manager
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(UserManager $manager, TokenStorageInterface $tokenStorage)
    {
        $this->manager = $manager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return JsonResponseData
     * @throws \Scriber\Bundle\CoreBundle\Exception\UserNotFoundException
     */
    public function __invoke(): JsonResponseData
    {
        $securityUser = $this->tokenStorage->getToken()->getUser();
        $user = $this->manager->getUser($securityUser->getUsername());

        return new JsonResponseData([
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'roles' => $user->getRoles(),
        ]);
    }
}

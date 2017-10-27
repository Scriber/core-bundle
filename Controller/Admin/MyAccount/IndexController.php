<?php
namespace Scriber\Bundle\CoreBundle\Controller\Admin\MyAccount;

use Scriber\Bundle\CoreBundle\User\Response\MyAccount\MyAccountResponse;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @return JsonResponse
     */
    public function __invoke()
    {
        $securityUser = $this->tokenStorage->getToken()->getUser();
        $user = $this->manager->getUser($securityUser->getUsername());

        return new JsonResponse(new MyAccountResponse($user));
    }
}

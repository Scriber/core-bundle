<?php
namespace Scriber\Bundle\CoreBundle\Controller\Admin\MyAccount;

use Scriber\Bundle\CoreBundle\Http\JsonResponseData;
use Scriber\Bundle\CoreBundle\ResponseData\UserData;
use Scriber\Bundle\CoreBundle\Security\SecurityUser;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
     * @throws \Scriber\Bundle\CoreBundle\Exception\UserNotFoundException|AccessDeniedHttpException
     */
    public function __invoke(): JsonResponseData
    {
        $securityUser = $this->tokenStorage->getToken()->getUser();
        if (!$securityUser instanceof SecurityUser) {
            throw new AccessDeniedHttpException();
        }

        $user = $this->manager->getUser($securityUser->getUsername());

        return new JsonResponseData(UserData::getArray($user));
    }
}

<?php
namespace Scriber\Bundle\CoreBundle\Controller\Admin\MyAccount;

use Rzeka\DataHandlerBundle\Api\ApiHandler;
use Scriber\Bundle\CoreBundle\User\Data\ChangePasswordData;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PasswordController
{
    /**
     * @var UserManager
     */
    private $manager;

    /**
     * @var ApiHandler
     */
    private $handler;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param UserManager $manager
     * @param ApiHandler $handler
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(UserManager $manager, ApiHandler $handler, TokenStorageInterface $tokenStorage)
    {
        $this->manager = $manager;
        $this->handler = $handler;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Scriber\Bundle\CoreBundle\Exception\UserNotFoundException
     */
    public function __invoke(Request $request): Response
    {
        $securityUser = $this->tokenStorage->getToken()->getUser();
        $user = $this->manager->getUser($securityUser->getUsername());

        $data = new ChangePasswordData();
        $result = $this->handler->handle(
            $request,
            $data
        );

        if ($result->isValid()) {
            $this->manager->updatePassword($user, $data->password);
        }

        return $this->handler->getResponseFromResult($result);
    }
}

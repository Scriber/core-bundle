<?php
namespace Scriber\Bundle\CoreBundle\Controller\Admin\MyAccount;

use Rzeka\DataHandlerBundle\Api\ApiHandler;
use Scriber\Bundle\CoreBundle\Http\JsonResponseData;
use Scriber\Bundle\CoreBundle\Http\UnprocessableEntityJsonResponseData;
use Scriber\Bundle\CoreBundle\Security\SecurityUser;
use Scriber\Bundle\CoreBundle\User\Data\UpdateData;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UpdateController
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
     * @return JsonResponseData
     * @throws \Scriber\Bundle\CoreBundle\Exception\UserNotFoundException
     */
    public function __invoke(Request $request): JsonResponseData
    {
        $securityUser = $this->tokenStorage->getToken()->getUser();
        if (!$securityUser instanceof SecurityUser) {
            throw new AccessDeniedHttpException();
        }
        $user = $this->manager->getUser($securityUser->getUsername());

        $data = new UpdateData($user);
        $result = $this->handler->handle(
            $request,
            $data
        );

        if ($result->isValid()) {
            $this->manager->updateUser($data);
            return new JsonResponseData([
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'roles' => $user->getRoles(),
            ]);
        }

        return new UnprocessableEntityJsonResponseData($result->getErrors());
    }
}

<?php
namespace Scriber\Bundle\CoreBundle\Controller\Admin\MyAccount;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Rzeka\DataHandlerBundle\Api\ApiHandler;
use Scriber\Bundle\CoreBundle\Security\SecurityUser;
use Scriber\Bundle\CoreBundle\User\Data\UpdateData;
use Scriber\Bundle\CoreBundle\User\Response\MyAccount\UpdateSuccessResponse;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @var JWTTokenManagerInterface
     */
    private $jwtManager;

    /**
     * @param UserManager $manager
     * @param ApiHandler $handler
     * @param TokenStorageInterface $tokenStorage
     * @param JWTTokenManagerInterface $jwtManager
     */
    public function __construct(UserManager $manager, ApiHandler $handler, TokenStorageInterface $tokenStorage, JWTTokenManagerInterface $jwtManager)
    {
        $this->manager = $manager;
        $this->handler = $handler;
        $this->tokenStorage = $tokenStorage;
        $this->jwtManager = $jwtManager;
    }

    /**
     * @return Response|JsonResponse
     */
    public function __invoke(Request $request): Response
    {
        $securityUser = $this->tokenStorage->getToken()->getUser();
        $user = $this->manager->getUser($securityUser->getUsername());

        $data = new UpdateData($user);
        $result = $this->handler->handle(
            $request,
            $data
        );

        if ($result->isValid()) {
            $this->manager->updateUser($data);

            $securityUser = new SecurityUser($data->getUser());
            $token = $this->jwtManager->create($securityUser);

            return new JsonResponse(new UpdateSuccessResponse($token));
        }

        return $this->handler->getResponseFromResult($result);
    }
}

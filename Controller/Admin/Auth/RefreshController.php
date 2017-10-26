<?php
namespace Scriber\Bundle\CoreBundle\Controller\Admin\Auth;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Scriber\Bundle\CoreBundle\Security\UserFactory;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RefreshController
{
    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthenticationSuccessHandler
     */
    private $successHandler;

    /**
     * @param UserFactory $userFactory
     * @param TokenStorageInterface $tokenStorage
     * @param AuthenticationSuccessHandler $successHandler
     */
    public function __construct(
        UserFactory $userFactory,
        TokenStorageInterface $tokenStorage,
        AuthenticationSuccessHandler $successHandler
    )
    {
        $this->userFactory = $userFactory;
        $this->tokenStorage = $tokenStorage;
        $this->successHandler = $successHandler;
    }

    /**
     * @return \Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse
     * @throws \RuntimeException
     */
    public function __invoke()
    {
        $securityUser = $this->tokenStorage->getToken()->getUser();
        $this->userFactory->loadUserByUsername($securityUser->getUsername());

        return $this->successHandler->handleAuthenticationSuccess($securityUser);
    }
}

<?php
namespace Scriber\Bundle\CoreBundle\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Scriber\Bundle\CoreBundle\Exception\UserNotLoggedInException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTGenerator
{
    /**
     * @var JWTTokenManagerInterface
     */
    private $JWTTokenManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param JWTTokenManagerInterface $JWTTokenManager
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(JWTTokenManagerInterface $JWTTokenManager, TokenStorageInterface $tokenStorage)
    {
        $this->JWTTokenManager = $JWTTokenManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return string
     * @throws UserNotLoggedInException
     */
    public function generateJWT(): string
    {
        $token = $this->tokenStorage->getToken();
        if (!$token || !($user = $token->getUser()) instanceof UserInterface) {
            throw new UserNotLoggedInException('User had to be logged in to issue JWT!');
        }

        return $this->JWTTokenManager->create($user);
    }
}

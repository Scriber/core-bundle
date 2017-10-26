<?php
namespace Scriber\Bundle\CoreBundle\Security;

use Scriber\Bundle\CoreBundle\Exception\UserNotFoundException;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserFactory implements UserProviderInterface
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
     * @param string $username
     *
     * @return SecurityUser
     * @throws UsernameNotFoundException
     */
    public function loadUserByUsername($username): SecurityUser
    {
        try {
            $user = $this->manager->getUser($username);
        } catch (UserNotFoundException $e) {
            throw new UsernameNotFoundException(sprintf('No user found for "%s"', $username));
        }

        return new SecurityUser($user);
    }

    /**
     * @param UserInterface $user
     *
     * @return SecurityUser
     * @throws UnsupportedUserException|UsernameNotFoundException
     */
    public function refreshUser(UserInterface $user): SecurityUser
    {
        if (!$user instanceof SecurityUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === SecurityUser::class;
    }
}

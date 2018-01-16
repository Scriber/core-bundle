<?php
namespace Scriber\Bundle\CoreBundle\Event\User;

use Scriber\Bundle\CoreBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractUserUpdateRolesEvent extends Event
{
    /**
     * @var array
     */
    private $roles;

    /**
     * @var User
     */
    private $user;

    /**
     * @param User $user
     * @param array $roles
     */
    public function __construct(User $user, array $roles)
    {
        $this->roles = $roles;
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }
}

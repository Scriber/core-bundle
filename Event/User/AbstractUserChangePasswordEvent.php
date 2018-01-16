<?php
namespace Scriber\Bundle\CoreBundle\Event\User;

use Scriber\Bundle\CoreBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractUserChangePasswordEvent extends Event
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @param User $user
     * @param string $password
     */
    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}

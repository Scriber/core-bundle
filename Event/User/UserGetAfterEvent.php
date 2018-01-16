<?php
namespace Scriber\Bundle\CoreBundle\Event\User;

use Scriber\Bundle\CoreBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class UserGetAfterEvent extends Event
{
    /**
     * @var User
     */
    private $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}

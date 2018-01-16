<?php
namespace Scriber\Bundle\CoreBundle\Event\User;

use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\User\Data\CreateData;
use Symfony\Component\EventDispatcher\Event;

class UserCreateAfterEvent extends Event
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var CreateData
     */
    private $data;

    /**
     * @param User $user
     * @param CreateData $data
     */
    public function __construct(User $user, CreateData $data)
    {
        $this->user = $user;
        $this->data = $data;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return CreateData
     */
    public function getData(): CreateData
    {
        return $this->data;
    }
}

<?php
namespace Scriber\Bundle\CoreBundle\Event\User;

use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\User\Data\UpdateData;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractUserUpdateEvent extends Event
{
    /**
     * @var UpdateData
     */
    private $data;

    /**
     * @var User
     */
    private $user;

    /**
     * @param User $user
     * @param UpdateData $data
     */
    public function __construct(User $user, UpdateData $data)
    {
        $this->user = $user;
        $this->data = $data;
    }

    /**
     * @return UpdateData
     */
    public function getData(): UpdateData
    {
        return $this->data;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}

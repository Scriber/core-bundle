<?php
namespace Scriber\Bundle\CoreBundle\User\Data;

use Rzeka\DataHandler\DataHydratableInterface;
use Rzeka\DataHandler\DataHydrationTrait;
use Scriber\Bundle\CoreBundle\Entity\User;

class UpdateData implements DataHydratableInterface
{
    use DataHydrationTrait;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $oldEmail;

    /**
     * @var User
     */
    private $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->email = $this->oldEmail = $user->getEmail();
        $this->name = $user->getName();
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

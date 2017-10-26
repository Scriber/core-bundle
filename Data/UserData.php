<?php
namespace Scriber\Bundle\CoreBundle\Data;

use Rzeka\DataHandler\DataHydratableInterface;
use Rzeka\DataHandler\DataHydrationTrait;
use Scriber\Bundle\CoreBundle\Entity\User;

class UserData implements DataHydratableInterface
{
    use DataHydrationTrait;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $oldEmail;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $oldPassword;

    /**
     * @param User|null $user
     */
    public function __construct(User $user = null)
    {
        if ($user) {
            $this->email = $this->oldEmail = $user->getEmail();
            $this->name = $user->getName();
        }
    }
}

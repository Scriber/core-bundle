<?php
namespace Scriber\Bundle\CoreBundle\User\Response\MyAccount;

use Scriber\Bundle\CoreBundle\Entity\User;

class MyAccountResponse implements \JsonSerializable
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
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->user->getName(),
            'email' => $this->user->getEmail(),
            'roles' => $this->user->getRoles()
        ];
    }
}

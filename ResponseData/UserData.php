<?php
namespace Scriber\Bundle\CoreBundle\ResponseData;

use Scriber\Bundle\CoreBundle\Entity\User;

class UserData
{
    /**
     * @param User $user
     *
     * @return array
     */
    public static function getArray(User $user)
    {
        return [
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'roles' => $user->getRoles(),
        ];
    }
}

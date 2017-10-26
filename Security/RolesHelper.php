<?php
namespace Scriber\Bundle\CoreBundle\Security;

class RolesHelper
{
    /**
     * @var array
     */
    private $roles;

    /**
     * @var array
     */
    private $hierarchy;

    /**
     * @param array $roles
     */
    public function __construct(array $roles)
    {
        $this->hierarchy = $roles;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        if ($this->roles === null) {
            $roles = [];

            array_walk_recursive($this->hierarchy, function ($role) use (&$roles) {
                if ($role) {
                    $roles[] = $role;
                }
            });

            $roles = array_merge(array_keys($this->hierarchy), $roles);
            $this->roles = array_unique($roles);
        }

        return $this->roles;
    }
}

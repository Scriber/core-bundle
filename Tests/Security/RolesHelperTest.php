<?php
namespace Scriber\Bundle\CoreBundle\Tests\Security;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Security\RolesHelper;

class RolesHelperTest extends TestCase
{
    public function testGetRoles()
    {
        $roles = [
            'ROLE_TEST' => null,
            'ROLE_ADMIN' => ['ROLE_EXAMPLE']
        ];

        $expected = [
            'ROLE_ADMIN',
            'ROLE_EXAMPLE',
            'ROLE_TEST',
        ];

        $helper = new RolesHelper($roles);
        $result = $helper->getRoles();

        static::assertEquals($expected, $result, '', 0, 10, true);
    }
}

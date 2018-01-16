<?php
namespace Scriber\Bundle\CoreBundle\Tests\Event\User;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Event\User\AbstractUserUpdateRolesEvent;
use Scriber\Bundle\CoreBundle\Event\User\UserUpdateRolesBeforeEvent;

class UserUpdateRolesBeforeEventTest extends TestCase
{
    public function testExtendsAbstractUserUpdateRolesEvent()
    {
        $event = new UserUpdateRolesBeforeEvent(
            $this->createMock(User::class),
            []
        );

        static::assertInstanceOf(AbstractUserUpdateRolesEvent::class, $event);
    }
}

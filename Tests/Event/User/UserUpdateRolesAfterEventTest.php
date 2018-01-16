<?php
namespace Scriber\Bundle\CoreBundle\Tests\Event\User;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Event\User\AbstractUserUpdateRolesEvent;
use Scriber\Bundle\CoreBundle\Event\User\UserUpdateRolesAfterEvent;

class UserUpdateRolesAfterEventTest extends TestCase
{
    public function testExtendsAbstractUserUpdateRolesEvent()
    {
        $event = new UserUpdateRolesAfterEvent(
            $this->createMock(User::class),
            []
        );

        static::assertInstanceOf(AbstractUserUpdateRolesEvent::class, $event);
    }
}

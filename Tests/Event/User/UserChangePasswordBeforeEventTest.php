<?php
namespace Scriber\Bundle\CoreBundle\Tests\Event\User;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Event\User\AbstractUserChangePasswordEvent;
use Scriber\Bundle\CoreBundle\Event\User\UserChangePasswordBeforeEvent;

class UserChangePasswordBeforeEventTest extends TestCase
{
    public function testExtendsAbstractUserChangePasswordEvent()
    {
        $event = new UserChangePasswordBeforeEvent(
            $this->createMock(User::class),
            ''
        );

        static::assertInstanceOf(AbstractUserChangePasswordEvent::class, $event);
    }
}

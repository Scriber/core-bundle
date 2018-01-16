<?php
namespace Scriber\Bundle\CoreBundle\Tests\Event\User;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Event\User\AbstractUserChangePasswordEvent;
use Scriber\Bundle\CoreBundle\Event\User\UserChangePasswordAfterEvent;

class UserChangePasswordAfterEventTest extends TestCase
{
    public function testImplementsAbstractUserChangePasswordEvent()
    {
        $event = new UserChangePasswordAfterEvent(
            $this->createMock(User::class),
            ''
        );

        static::assertInstanceOf(AbstractUserChangePasswordEvent::class, $event);
    }
}

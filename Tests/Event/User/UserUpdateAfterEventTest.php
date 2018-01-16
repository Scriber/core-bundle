<?php
namespace Scriber\Bundle\CoreBundle\Tests\Event\User;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Event\User\AbstractUserUpdateEvent;
use Scriber\Bundle\CoreBundle\Event\User\UserUpdateAfterEvent;
use Scriber\Bundle\CoreBundle\User\Data\UpdateData;

class UserUpdateAfterEventTest extends TestCase
{
    public function testExtendsAbstractUserUpdateEvent()
    {
        $event = new UserUpdateAfterEvent(
            $this->createMock(User::class),
            $this->createMock(UpdateData::class)
        );

        static::assertInstanceOf(AbstractUserUpdateEvent::class, $event);
    }
}

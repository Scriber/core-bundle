<?php
namespace Scriber\Bundle\CoreBundle\Tests\Event\User;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Event\User\AbstractUserUpdateEvent;
use Scriber\Bundle\CoreBundle\Event\User\UserUpdateBeforeEvent;
use Scriber\Bundle\CoreBundle\User\Data\UpdateData;

class UserUpdateBeforeEventTest extends TestCase
{
    public function testExtendsAbstractUserUpdateEvent()
    {
        $event = new UserUpdateBeforeEvent(
            $this->createMock(User::class),
            $this->createMock(UpdateData::class)
        );

        static::assertInstanceOf(AbstractUserUpdateEvent::class, $event);
    }
}

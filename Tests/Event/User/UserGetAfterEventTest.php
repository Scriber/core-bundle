<?php
namespace Scriber\Bundle\CoreBundle\Tests\Event\User;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Event\User\UserGetAfterEvent;

class UserGetAfterEventTest extends TestCase
{
    public function testGetUser()
    {
        $user = $this->createMock(User::class);

        $event = new UserGetAfterEvent($user);
        static::assertEquals($user, $event->getUser());
    }
}

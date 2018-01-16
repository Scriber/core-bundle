<?php
namespace Scriber\Bundle\CoreBundle\Tests\Event\User;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Event\User\UserGetBeforeEvent;

class UserGetBeforeEventTest extends TestCase
{
    public function testGetEmail()
    {
        $email = 'test@example.com';
        $event = new UserGetBeforeEvent($email);
        static::assertEquals($email, $event->getEmail());
    }

    public function testSetEmail()
    {
        $email = 'test@example.com';
        $event = new UserGetBeforeEvent('');

        $event->setEmail($email);
        static::assertEquals($email, $event->getEmail());
    }
}

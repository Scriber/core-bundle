<?php
namespace Scriber\Bundle\CoreBundle\Tests\Event\User;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Event\User\UserCreateBeforeEvent;
use Scriber\Bundle\CoreBundle\User\Data\CreateData;

class UserCreateBeforeEventTest extends TestCase
{
    public function testGetData()
    {
        $data = $this->createMock(CreateData::class);
        $event = new UserCreateBeforeEvent($data);
        static::assertEquals($data, $event->getData());
    }
}

<?php
namespace Scriber\Bundle\CoreBundle\Tests\Event\User;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Event\User\UserCreateAfterEvent;
use Scriber\Bundle\CoreBundle\User\Data\CreateData;

class UserCreateAfterEventTest extends TestCase
{
    /**
     * @var MockObject|User
     */
    private $user;

    /**
     * @var MockObject|CreateData
     */
    private $data;

    protected function setUp()
    {
        $this->user = $this->createMock(User::class);
        $this->data = $this->createMock(CreateData::class);
    }

    protected function tearDown()
    {
        $this->user = null;
        $this->data = null;
    }

    public function testGetUser()
    {
        $event = new UserCreateAfterEvent($this->user, $this->data);
        static::assertEquals($this->user, $event->getUser());
    }

    public function testGetData()
    {
        $event = new UserCreateAfterEvent($this->user, $this->data);
        static::assertEquals($this->data, $event->getData());
    }
}

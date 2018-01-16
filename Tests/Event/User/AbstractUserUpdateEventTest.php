<?php
namespace Scriber\Bundle\CoreBundle\Tests\Event\User;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Event\User\AbstractUserUpdateEvent;
use Scriber\Bundle\CoreBundle\User\Data\UpdateData;

class AbstractUserUpdateEventTest extends TestCase
{
    /**
     * @var MockObject|User
     */
    private $user;

    /**
     * @var MockObject|UpdateData
     */
    private $data;

    protected function setUp()
    {
        $this->user = $this->createMock(User::class);
        $this->data = $this->createMock(UpdateData::class);
    }

    protected function tearDown()
    {
        $this->user = null;
        $this->data = null;
    }

    public function testGetUser()
    {
        $event = $this->getClass($this->user, $this->data);
        static::assertEquals($this->user, $event->getUser());
    }

    public function testGetData()
    {
        $event = $this->getClass($this->user, $this->data);
        static::assertEquals($this->data, $event->getData());
    }

    /**
     * @param array ...$args
     *
     * @return AbstractUserUpdateEvent
     */
    private function getClass(...$args)
    {
        return new class(...$args) extends AbstractUserUpdateEvent {};
    }
}

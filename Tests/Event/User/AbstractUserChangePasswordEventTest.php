<?php
namespace Scriber\Bundle\CoreBundle\Tests\Event\User;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Event\User\AbstractUserChangePasswordEvent;

class AbstractUserChangePasswordEventTest extends TestCase
{
    /**
     * @var MockObject|User
     */
    private $user;

    protected function setUp()
    {
        $this->user = $this->createMock(User::class);
    }

    protected function tearDown()
    {
        $this->user = null;
    }

    public function testGetUser()
    {
        $event = $this->getClass($this->user, '');
        static::assertEquals($this->user, $event->getUser());
    }

    public function testGetPassword()
    {
        $password = 'secret';

        $event = $this->getClass($this->user, $password);
        static::assertEquals($password, $event->getPassword());
    }

    public function testSetPassword()
    {
        $password = 'secret';

        $event = $this->getClass($this->user, '');
        $event->setPassword($password);
        static::assertEquals($password, $event->getPassword());
    }

    /**
     * @param array ...$args
     *
     * @return AbstractUserChangePasswordEvent
     */
    private function getClass(...$args)
    {
        return new class(...$args) extends AbstractUserChangePasswordEvent {};
    }
}

<?php
namespace Scriber\Bundle\CoreBundle\Tests\Event\User;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Event\User\AbstractUserUpdateRolesEvent;

class AbstractUserUpdateRolesEventTest extends TestCase
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
        $event = $this->getClass($this->user, []);
        static::assertEquals($this->user, $event->getUser());
    }

    public function testGetRoles()
    {
        $roles = ['TEST'];

        $event = $this->getClass($this->user, $roles);
        static::assertEquals($roles, $event->getRoles());
    }

    public function testSetRoles()
    {
        $roles = ['TEST'];

        $event = $this->getClass($this->user, []);
        $event->setRoles($roles);
        static::assertEquals($roles, $event->getRoles());
    }

    /**
     * @param array ...$args
     *
     * @return AbstractUserUpdateRolesEvent
     */
    private function getClass(...$args)
    {
        return new class(...$args) extends AbstractUserUpdateRolesEvent {};
    }
}

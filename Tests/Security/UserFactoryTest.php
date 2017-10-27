<?php
namespace Scriber\Bundle\CoreBundle\Tests\Security;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Exception\UserNotFoundException;
use Scriber\Bundle\CoreBundle\Security\SecurityUser;
use Scriber\Bundle\CoreBundle\Security\UserFactory;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|UserManager
     */
    private $manager;

    public function setUp()
    {
        $this->manager = $this->createMock(UserManager::class);
    }

    public function tearDown()
    {
        $this->manager = null;
    }

    public function testLoadUserByUsername()
    {
        $user = $this->createMock(User::class);
        $this->manager
            ->expects(static::once())
            ->method('getUser')
            ->willReturn($user);

        $factory = new UserFactory($this->manager);
        $result = $factory->loadUserByUsername('');

        static::assertInstanceOf(SecurityUser::class, $result);
    }

    public function testLoadUserByUsernameNotFound()
    {
        $this->manager
            ->expects(static::once())
            ->method('getUser')
            ->willThrowException(new UserNotFoundException());

        $this->expectException(UsernameNotFoundException::class);
        $factory = new UserFactory($this->manager);
        $factory->loadUserByUsername('');
    }

    public function testRefreshUser()
    {
        $email = 'test@example.com';

        $user = $this->createMock(User::class);
        $user
            ->method('getEmail')
            ->willReturn($email);

        $this->manager
            ->expects(static::once())
            ->method('getUser')
            ->with($email)
            ->willReturn($user);

        $securityUser = new SecurityUser($user);

        $factory = new UserFactory($this->manager);
        $result = $factory->refreshUser($securityUser);

        static::assertEquals($securityUser, $result);
    }

    public function testRefreshUserInvalidObject()
    {
        $securityUser = new class implements UserInterface {
            public function eraseCredentials()
            {
            }

            public function getPassword()
            {
            }

            public function getRoles()
            {
            }

            public function getSalt()
            {
            }

            public function getUsername()
            {
            }
        };

        $factory = new UserFactory($this->manager);

        $this->expectException(UnsupportedUserException::class);
        $factory->refreshUser($securityUser);
    }

    /**
     * @param $class
     * @param $expected
     *
     * @dataProvider supportsClassProvider
     */
    public function testSupportsClass($class, $expected)
    {
        $factory = new UserFactory($this->manager);
        $result = $factory->supportsClass($class);

        static::assertEquals($expected, $result);
    }

    public function supportsClassProvider()
    {
        return [
            [SecurityUser::class, true],
            [\stdClass::class, false]
        ];
    }

}


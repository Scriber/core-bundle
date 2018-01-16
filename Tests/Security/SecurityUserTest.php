<?php
namespace Scriber\Bundle\CoreBundle\Tests\Security;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Security\SecurityUser;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;

class SecurityUserTest extends TestCase
{
    private $email = 'test@example.com';
    private $password = 'secret';
    private $roles = ['ROLE_ADMIN'];

    public function testConstructor()
    {
        $email = 'test@example.com';
        $password = 'secret';
        $roles = ['ROLE_ADMIN'];

        $user = $this->getUserMock();
        $securityUser = new SecurityUser($user);

        static::assertEquals($email, $securityUser->getEmail());
        static::assertEquals($password, $securityUser->getPassword());
        static::assertEquals($roles, $securityUser->getRoles());
        static::assertTrue($securityUser->isEnabled());
    }

    public function testSerialize()
    {
        $user = $this->getUserMock();
        $securityUser = new SecurityUser($user);

        $resultSerialized = serialize($securityUser);
        $resultUnserialized = unserialize($resultSerialized);

        static::assertEquals($securityUser, $resultUnserialized);
    }

    public function testDummyMethods()
    {
        $user = $this->getUserMock();
        $securityUser = new SecurityUser($user);

        static::assertNull($securityUser->getSalt());
        static::assertNull($securityUser->eraseCredentials());
        static::assertTrue($securityUser->isAccountNonExpired());
        static::assertTrue($securityUser->isAccountNonLocked());
        static::assertTrue($securityUser->isCredentialsNonExpired());
    }

    public function testEncoderAware()
    {
        $user = $this->getUserMock();
        $securityUser = new SecurityUser($user);

        static::assertInstanceOf(EncoderAwareInterface::class, $securityUser);
        static::assertEquals('scriber', $securityUser->getEncoderName());
    }

    private function getUserMock()
    {
        $user = $this->createMock(User::class);

        $user
            ->expects(static::once())
            ->method('getEmail')
            ->willReturn($this->email);

        $user
            ->expects(static::once())
            ->method('getPassword')
            ->willReturn($this->password);

        $user
            ->expects(static::once())
            ->method('getRoles')
            ->willReturn($this->roles);

        $user
            ->expects(static::once())
            ->method('isActive')
            ->willReturn(true);

        return $user;
    }
}

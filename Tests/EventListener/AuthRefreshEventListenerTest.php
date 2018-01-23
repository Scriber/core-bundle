<?php
namespace Scriber\Bundle\CoreBundle\Tests\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\EventListener\AuthenticationSuccessEventListener;
use Scriber\Bundle\CoreBundle\Security\SecurityUser;
use Scriber\Bundle\CoreBundle\User\UserManager;

class AuthenticationSuccessEventListenerTest extends TestCase
{
    public function testOnAuthenticationSuccessResponse()
    {
        $username = 'test@example.com';
        $data = [];

        $email = 'test@example.com';
        $name = 'John Doe';
        $roles = ['ROLE_TEST'];

        $securityUser = $this->createMock(SecurityUser::class);
        $securityUser
            ->expects(static::once())
            ->method('getUsername')
            ->willreturn($username);

        $event = $this->createMock(AuthenticationSuccessEvent::class);
        $event
            ->expects(static::once())
            ->method('getUser')
            ->willReturn($securityUser);

        $event
            ->expects(static::once())
            ->method('getData')
            ->willReturn($data);

        $event
            ->expects(static::once())
            ->method('setData')
            ->with([
                'user' => [
                    'email' => $email,
                    'name' => $name,
                    'roles' => $roles
                ]
            ]);

        $user = $this->createMock(User::class);
        $user
            ->expects(static::once())
            ->method('getEmail')
            ->willReturn($email);

        $user
            ->expects(static::once())
            ->method('getName')
            ->willReturn($name);

        $user
            ->expects(static::once())
            ->method('getRoles')
            ->willReturn($roles);

        $manager = $this->createMock(UserManager::class);
        $manager
            ->expects(static::once())
            ->method('getUser')
            ->with($username)
            ->willReturn($user);

        $listener = new AuthenticationSuccessEventListener($manager);
        $listener->onAuthenticationSuccessResponse($event);
    }
}

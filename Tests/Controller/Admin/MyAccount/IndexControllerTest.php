<?php
namespace Scriber\Bundle\CoreBundle\Tests\Controller\Auth;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Controller\Admin\MyAccount\IndexController;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Security\SecurityUser;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class IndexControllerTest extends TestCase
{
    /**
     * @var UserManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $manager;

    /**
     * @var TokenStorageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tokenStorage;

    public function setUp()
    {
        $this->manager = $this->createMock(UserManager::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
    }

    public function tearDown()
    {
        $this->manager = null;
        $this->tokenStorage = null;
    }

    public function testInvoke()
    {
        $email = 'test@example.com';
        $name = 'John Doe';
        $roles = [
            'ROLE_TEST'
        ];

        $expectedResult = [
            'name' => $name,
            'email' => $email,
            'roles' => $roles
        ];

        $token = $this->createMock(TokenInterface::class);
        $securityUser = $this->createMock(SecurityUser::class);
        $user = $this->createMock(User::class);

        $token
            ->expects(static::once())
            ->method('getUser')
            ->willReturn($securityUser);

        $this->tokenStorage
            ->expects(static::once())
            ->method('getToken')
            ->willReturn($token);

        $securityUser
            ->expects(static::once())
            ->method('getUsername')
            ->willReturn($email);

        $this->manager
            ->expects(static::once())
            ->method('getUser')
            ->with($email)
            ->willReturn($user);

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

        $controller = new IndexController($this->manager, $this->tokenStorage);
        $result = $controller();
        $resultString = $result->getContent();

        static::assertInstanceOf(JsonResponse::class, $result);
        static::assertJson($resultString);

        $resultArray = json_decode($resultString, true);
        static::assertEquals($expectedResult, $resultArray);
    }
}

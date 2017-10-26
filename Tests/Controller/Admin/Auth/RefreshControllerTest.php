<?php
namespace Scriber\Bundle\CoreBundle\Tests\Controller\Admin\Auth;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Controller\Admin\Auth\RefreshController;
use Scriber\Bundle\CoreBundle\Security\SecurityUser;
use Scriber\Bundle\CoreBundle\Security\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class RefreshControllerTest extends TestCase
{
    /**
     * @var UserFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $userFactory;

    /**
     * @var TokenStorageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tokenStorage;

    /**
     * @var AuthenticationSuccessHandler|\PHPUnit_Framework_MockObject_MockObject
     */
    private $successHandler;

    public function setUp()
    {
        $this->userFactory = $this->createMock(UserFactory::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->successHandler = $this->createMock(AuthenticationSuccessHandler::class);
    }

    public function tearDown()
    {
        $this->userFactory = null;
        $this->tokenStorage = null;
        $this->successHandler = null;
    }

    public function testInvoke()
    {
        $email = 'test@example.com';

        $response = $this->createMock(Response::class);

        $token = $this->getTokenMock();
        $user = $this->getUserMock();

        $user
            ->expects(static::once())
            ->method('getUsername')
            ->willReturn($email);

        $token
            ->expects(static::once())
            ->method('getUser')
            ->willReturn($user);

        $this->tokenStorage
            ->expects(static::once())
            ->method('getToken')
            ->willReturn($token);

        $this->userFactory
            ->expects(static::once())
            ->method('loadUserByUsername')
            ->with($email)
            ->willReturn($user);

        $this->successHandler
            ->expects(static::once())
            ->method('handleAuthenticationSuccess')
            ->with($user)
            ->willReturn($response);

        $controller = new RefreshController($this->userFactory, $this->tokenStorage, $this->successHandler);
        $result = $controller();

        static::assertEquals($response, $result);
    }

    private function getTokenMock()
    {
        return $this->createMock(TokenInterface::class);
    }

    private function getUserMock()
    {
        return $this->createMock(SecurityUser::class);
    }
}

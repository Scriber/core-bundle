<?php
namespace Scriber\Bundle\CoreBundle\Tests\Controller\Admin\MyAccount;

use PHPUnit\Framework\TestCase;
use Rzeka\DataHandler\DataHandlerResult;
use Rzeka\DataHandlerBundle\Api\ApiHandler;
use Scriber\Bundle\CoreBundle\Controller\Admin\MyAccount\PasswordController;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Security\SecurityUser;
use Scriber\Bundle\CoreBundle\User\Data\ChangePasswordData;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PasswordControllerTest extends TestCase
{
    /**
     * @var UserManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $manager;

    /**
     * @var ApiHandler|\PHPUnit_Framework_MockObject_MockObject
     */
    private $apiHandler;

    /**
     * @var TokenStorageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tokenStorage;

    public function setUp()
    {
        $this->manager = $this->createMock(UserManager::class);
        $this->apiHandler = $this->createMock(ApiHandler::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
    }

    public function tearDown()
    {
        $this->manager = null;
        $this->apiHandler = null;
        $this->tokenStorage = null;
    }

    public function testInvoke()
    {
        $email = 'test@example.com';
        $password = 'secret';
        $expectedResult = $this->createMock(Response::class);

        $token = $this->createMock(TokenInterface::class);
        $securityUser = $this->createMock(SecurityUser::class);

        $user = $this->createMock(User::class);
        $request = $this->createMock(Request::class);

        $handlerResult = $this->createMock(DataHandlerResult::class);

        $this->tokenStorage
            ->expects(static::once())
            ->method('getToken')
            ->willReturn($token);

        $token
            ->expects(static::once())
            ->method('getUser')
            ->willReturn($securityUser);

        $securityUser
            ->expects(static::once())
            ->method('getUsername')
            ->willReturn($email);

        $this->manager
            ->expects(static::once())
            ->method('getUser')
            ->with($email)
            ->willReturn($user);

        $this->apiHandler
            ->expects(static::once())
            ->method('handle')
            ->with(
                $request,
                static::callback(function ($v) use ($password) {
                    if ($v instanceof ChangePasswordData) {
                        $v->password = $password;
                    }

                    return $v instanceof ChangePasswordData;
                })
            )
            ->willReturn($handlerResult);

        $handlerResult
            ->expects(static::once())
            ->method('isValid')
            ->willReturn(true);

        $this->manager
            ->expects(static::once())
            ->method('updatePassword')
            ->with($user, $password);

        $this->apiHandler
            ->expects(static::once())
            ->method('getResponseFromResult')
            ->willReturn($expectedResult);

        $controller = new PasswordController(
            $this->manager,
            $this->apiHandler,
            $this->tokenStorage
        );

        $result = $controller($request);
        static::assertEquals($expectedResult, $result);
    }

    public function testInvokeInvalid()
    {
        $email = 'test@example.com';
        $password = 'secret';
        $expectedResult = $this->createMock(Response::class);

        $token = $this->createMock(TokenInterface::class);
        $securityUser = $this->createMock(SecurityUser::class);

        $user = $this->createMock(User::class);
        $request = $this->createMock(Request::class);

        $handlerResult = $this->createMock(DataHandlerResult::class);

        $this->tokenStorage
            ->expects(static::once())
            ->method('getToken')
            ->willReturn($token);

        $token
            ->expects(static::once())
            ->method('getUser')
            ->willReturn($securityUser);

        $securityUser
            ->expects(static::once())
            ->method('getUsername')
            ->willReturn($email);

        $this->manager
            ->expects(static::once())
            ->method('getUser')
            ->with($email)
            ->willReturn($user);

        $this->apiHandler
            ->expects(static::once())
            ->method('handle')
            ->with(
                $request,
                static::callback(function ($v) use ($password) {
                    if ($v instanceof ChangePasswordData) {
                        $v->password = $password;
                    }

                    return $v instanceof ChangePasswordData;
                })
            )
            ->willReturn($handlerResult);

        $handlerResult
            ->expects(static::once())
            ->method('isValid')
            ->willReturn(false);

        $this->manager
            ->expects(static::never())
            ->method('updatePassword');

        $this->apiHandler
            ->expects(static::once())
            ->method('getResponseFromResult')
            ->willReturn($expectedResult);

        $controller = new PasswordController(
            $this->manager,
            $this->apiHandler,
            $this->tokenStorage
        );

        $result = $controller($request);
        static::assertEquals($expectedResult, $result);
    }

    public function testInvokeNotLoggedIn()
    {
        $token = $this->createMock(TokenInterface::class);
        $request = $this->createMock(Request::class);

        $this->tokenStorage
            ->expects(static::once())
            ->method('getToken')
            ->willReturn($token);

        $token
            ->expects(static::once())
            ->method('getUser')
            ->willReturn('');

        $this->manager
            ->expects(static::never())
            ->method('getUser');

        $this->expectException(AccessDeniedHttpException::class);
        $controller = new PasswordController(
            $this->manager,
            $this->apiHandler,
            $this->tokenStorage
        );
        $controller($request);
    }
}

<?php
namespace Scriber\Bundle\CoreBundle\Tests\Controller\Admin\MyAccount;

use PHPUnit\Framework\TestCase;
use Rzeka\DataHandler\DataHandlerResult;
use Rzeka\DataHandlerBundle\Api\ApiHandler;
use Scriber\Bundle\CoreBundle\Controller\Admin\MyAccount\UpdateController;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Http\JsonResponseData;
use Scriber\Bundle\CoreBundle\Http\UnprocessableEntityJsonResponseData;
use Scriber\Bundle\CoreBundle\Security\SecurityUser;
use Scriber\Bundle\CoreBundle\User\Data\UpdateData;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UpdateControllerTest extends TestCase
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
        $name = 'John Doe';
        $roles = ['TEST'];

        $expectedResult = [
            'email' => $email,
            'name' => $name,
            'roles' => $roles,
        ];

        $token = $this->createMock(TokenInterface::class);
        $securityUser = $this->createMock(SecurityUser::class);

        $user = $this->createMock(User::class);
        $request = $this->createMock(Request::class);

        $handlerResult = $this->createMock(DataHandlerResult::class);

        $dataValidator = static::callback(function ($v) use ($user) {
            return $v instanceof UpdateData &&
                   $v->getUser() === $user;
        });

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
                $dataValidator
            )
            ->willReturn($handlerResult);

        $handlerResult
            ->expects(static::once())
            ->method('isValid')
            ->willReturn(true);

        $this->manager
            ->expects(static::once())
            ->method('updateUser')
            ->with($dataValidator);

        $user
            ->method('getEmail')
            ->willReturn($email);

        $user
            ->method('getName')
            ->willReturn($name);

        $user
            ->method('getRoles')
            ->willReturn($roles);

        $this->apiHandler
            ->expects(static::never())
            ->method('getResponseFromResult');

        $controller = new UpdateController(
            $this->manager,
            $this->apiHandler,
            $this->tokenStorage
        );

        $result = $controller($request);

        static::assertInstanceOf(JsonResponseData::class, $result);
        static::assertEquals($expectedResult, $result->getData());
        static::assertEquals(200, $result->getStatus());
        static::assertEmpty($result->getHeaders());
    }

    public function testInvokeWithError()
    {
        $email = 'test@example.com';
        $errors = ['errors'];

        $token = $this->createMock(TokenInterface::class);
        $securityUser = $this->createMock(SecurityUser::class);

        $user = $this->createMock(User::class);
        $request = $this->createMock(Request::class);

        $handlerResult = $this->createMock(DataHandlerResult::class);

        $dataValidator = static::callback(function ($v) use ($user) {
            return $v instanceof UpdateData &&
                   $v->getUser() === $user;
        });

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
                $dataValidator
            )
            ->willReturn($handlerResult);

        $handlerResult
            ->expects(static::once())
            ->method('isValid')
            ->willReturn(false);

        $this->manager
            ->expects(static::never())
            ->method('updateUser');

        $handlerResult
            ->expects(static::once())
            ->method('getErrors')
            ->willReturn($errors);

        $controller = new UpdateController(
            $this->manager,
            $this->apiHandler,
            $this->tokenStorage
        );

        $result = $controller($request);

        static::assertInstanceOf(UnprocessableEntityJsonResponseData::class, $result);
        static::assertEquals($errors, $result->getData());
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
        $controller = new UpdateController(
            $this->manager,
            $this->apiHandler,
            $this->tokenStorage
        );
        $controller($request);
    }
}

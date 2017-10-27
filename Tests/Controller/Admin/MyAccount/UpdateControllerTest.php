<?php
namespace Scriber\Bundle\CoreBundle\Tests\User\Response\MyAccount;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\TestCase;
use Rzeka\DataHandler\DataHandlerResult;
use Rzeka\DataHandlerBundle\Api\ApiHandler;
use Scriber\Bundle\CoreBundle\Controller\Admin\MyAccount\UpdateController;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\Security\SecurityUser;
use Scriber\Bundle\CoreBundle\User\Data\UpdateData;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * @var JWTTokenManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $jwtManager;

    public function setUp()
    {
        $this->manager = $this->createMock(UserManager::class);
        $this->apiHandler = $this->createMock(ApiHandler::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->jwtManager = $this->createMock(JWTTokenManagerInterface::class);
    }

    public function tearDown()
    {
        $this->manager = null;
        $this->apiHandler = null;
        $this->tokenStorage = null;
        $this->jwtManager = null;
    }

    public function testInvoke()
    {
        $email = 'test@example.com';
        $jwtToken = 'test';
        $expectedResult = ['token' => $jwtToken];

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

        $this->jwtManager
            ->expects(static::once())
            ->method('create')
            ->with(static::callback(function ($v) use ($user) {
                return $v instanceof SecurityUser &&
                       $v->getUsername() === $user->getEmail();
            }))
            ->willReturn($jwtToken);

        $this->apiHandler
            ->expects(static::never())
            ->method('getResponseFromResult');

        $controller = new UpdateController(
            $this->manager,
            $this->apiHandler,
            $this->tokenStorage,
            $this->jwtManager
        );

        $result = $controller($request);

        static::assertInstanceOf(JsonResponse::class, $result);
        static::assertJson($result->getContent());

        $decodedJson = json_decode($result->getContent(), true);

        static::assertEquals($expectedResult, $decodedJson);
    }

    public function testInvokeWithError()
    {
        $email = 'test@example.com';
        $expectedResponse = $this->createMock(Response::class);

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

        $this->jwtManager
            ->expects(static::never())
            ->method('create');

        $this->apiHandler
            ->expects(static::once())
            ->method('getResponseFromResult')
            ->with($handlerResult)
            ->willReturn($expectedResponse);

        $controller = new UpdateController(
            $this->manager,
            $this->apiHandler,
            $this->tokenStorage,
            $this->jwtManager
        );

        $result = $controller($request);

        static::assertEquals($expectedResponse, $result);
    }
}

<?php
namespace Scriber\Bundle\CoreBundle\Tests\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Exception\UserNotLoggedInException;
use Scriber\Bundle\CoreBundle\Security\JWTGenerator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTGeneratorTest extends TestCase
{
    /**
     * @var MockObject|JWTTokenManagerInterface
     */
    private $jwtToken;

    /**
     * @var MockObject|TokenStorageInterface
     */
    private $tokenStorage;

    protected function setUp()
    {
        $this->jwtToken = $this->createMock(JWTTokenManagerInterface:: class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
    }

    protected function tearDown()
    {
        $this->jwtToken = null;
        $this->tokenStorage = null;
    }

    public function testGenerateJWTUserNotLoggedIn()
    {
        $token = $this->createMock(TokenInterface::class);

        $this->tokenStorage
            ->expects(static::once())
            ->method('getToken')
            ->willReturn($token);

        $token
            ->expects(static::once())
            ->method('getUser')
            ->willReturn('anon.');

        $this->expectException(UserNotLoggedInException::class);
        $this->expectExceptionMessage('User had to be logged in to issue JWT!');

        $generator = new JWTGenerator($this->jwtToken, $this->tokenStorage);
        $generator->generateJWT();
    }

    public function testGenerateJWTUserNoToken()
    {
        $this->tokenStorage
            ->expects(static::once())
            ->method('getToken')
            ->willReturn(null);

        $this->expectException(UserNotLoggedInException::class);
        $this->expectExceptionMessage('User had to be logged in to issue JWT!');

        $generator = new JWTGenerator($this->jwtToken, $this->tokenStorage);
        $generator->generateJWT();
    }

    public function testGenerateJWT()
    {
        $token = $this->createMock(TokenInterface::class);
        $user = $this->createMock(UserInterface::class);

        $jwtToken = 'test';

        $this->tokenStorage
            ->expects(static::once())
            ->method('getToken')
            ->willReturn($token);

        $token
            ->expects(static::once())
            ->method('getUser')
            ->willReturn($user);

        $this->jwtToken
            ->expects(static::once())
            ->method('create')
            ->willReturn($jwtToken);

        $generator = new JWTGenerator($this->jwtToken, $this->tokenStorage);
        $result = $generator->generateJWT();

        static::assertEquals($jwtToken, $result);
    }

}

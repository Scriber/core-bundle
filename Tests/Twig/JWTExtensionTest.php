<?php
namespace Scriber\Bundle\CoreBundle\Tests\Twig;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Exception\UserNotLoggedInException;
use Scriber\Bundle\CoreBundle\Security\JWTGenerator;
use Scriber\Bundle\CoreBundle\Twig\JWTExtension;
use Twig\TwigFunction;

class JWTExtensionTest extends TestCase
{
    public function testGetFunctions()
    {
        $ext = new JWTExtension($this->createMock(JWTGenerator::class));

        $result = $ext->getFunctions();

        static::assertCount(1, $result);
        $function = reset($result);

        static::assertInstanceOf(TwigFunction::class, $function);
        static::assertEquals('scriber_core_jwt', $function->getName());
    }

    public function testGenerateJWT()
    {
        $jwtToken = 'test';

        $generator = $this->createMock(JWTGenerator::class);
        $generator
            ->expects(static::once())
            ->method('generateJWT')
            ->willReturn($jwtToken);

        $ext = new JWTExtension($generator);
        $result = $ext->generateJWT();

        static::assertEquals($jwtToken, $result);
    }

    public function testGenerateJWTUserNotLoggedIn()
    {
        $generator = $this->createMock(JWTGenerator::class);
        $generator
            ->expects(static::once())
            ->method('generateJWT')
            ->willThrowException(new UserNotLoggedInException());

        $ext = new JWTExtension($generator);
        $result = $ext->generateJWT();

        static::assertEquals('', $result);
    }
}

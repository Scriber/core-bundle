<?php
namespace Scriber\Bundle\CoreBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Entity\User;

class UserTest extends TestCase
{
    public function testConstructor()
    {
        $email = 'test@example.com';
        $name = 'John Doe';
        $roles = [];

        $user = new User($email, $name);

        static::assertEquals($email, $user->getEmail());
        static::assertEquals($name, $user->getName());
        static::assertEquals($roles, $user->getRoles());
        static::assertEquals('', $user->getPassword());
        static::assertTrue($user->isActive());
    }

    /**
     * @dataProvider throwErrorExceptionIfNoDataProvider
     */
    public function testThrowErrorExceptionIfNoData($method)
    {
        $this->expectException(\TypeError::class);

        $user = new User('', '');
        $user->{$method}();
    }

    /**
     * @dataProvider setterAndGetterProvider
     */
    public function testSetterAndGetter($value, $setMethod, $getMethod)
    {
        $user = new User('', '');

        $user->{$setMethod}($value);

        $result = $user->{$getMethod}();
        static::assertEquals($value, $result);
    }

    public function testDefaultHasNoResetToken()
    {
        $user = new User('', '');

        static::assertFalse($user->hasResetToken());
    }

    public function testHasResetToken()
    {
        $user = new User('', '');

        $user->setResetToken('token', new \DateTime('+1 day'));
        static::assertTrue($user->hasResetToken());
    }

    public function testClearResetToken()
    {
        $user = new User('', '');

        $user->setResetToken('token', new \DateTime());
        $user->clearResetToken();

        static::assertFalse($user->hasResetToken());
    }

    public function testHasExpiredResetToken()
    {
        $user = new User('', '');
        $user->setResetToken('token', new \DateTime('-1 day'));

        static::assertFalse($user->hasResetToken());
    }

    /**
     * @return array
     */
    public function throwErrorExceptionIfNoDataProvider()
    {
        return [
            ['getId'],
            ['getResetToken'],
            ['getResetTokenTimeout']
        ];
    }

    /**
     * @return array
     */
    public function setterAndGetterProvider()
    {
        return [
            ['test', 'setName', 'getName'],
            ['test', 'setPassword', 'getPassword'],
            ['test@example.com', 'setEmail', 'getEmail'],
            [['ROLE_TEST'], 'setRoles', 'getRoles'],
            [false, 'setActive', 'isActive']
        ];
    }

}

<?php
namespace Scriber\Bundle\CoreBundle\Tests\Data;

use PHPUnit\Framework\TestCase;
use Rzeka\DataHandler\DataHydratableInterface;
use Scriber\Bundle\CoreBundle\Data\UserData;
use Scriber\Bundle\CoreBundle\Entity\User;

class UserDataTest extends TestCase
{
    public function testImplementedInterfaces()
    {
        $data = new UserData();

        static::assertInstanceOf(DataHydratableInterface::class, $data);
    }

    public function testConstructor()
    {
        $data = new UserData();

        static::assertNull($data->email);
        static::assertNull($data->name);
    }

    public function testConstructorWithUser()
    {
        $email = 'admin@example.com';
        $name = 'John Doe';

        $user = $this->createMock(User::class);
        $user
            ->expects(static::once())
            ->method('getEmail')
            ->willReturn($email);

        $user
            ->expects(static::once())
            ->method('getName')
            ->willReturn($name);

        $data = new UserData($user);

        static::assertEquals($email, $data->email);
        static::assertEquals($name, $data->name);
    }
}

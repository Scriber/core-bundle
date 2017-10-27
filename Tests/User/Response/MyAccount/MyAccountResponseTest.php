<?php
namespace Scriber\Bundle\CoreBundle\User\Response\MyAccount;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Entity\User;

class MyAccountResponseTest extends TestCase
{
    public function testImplementsJsonSerializable()
    {
        $user = $this->createMock(User::class);
        $response = new MyAccountResponse($user);

        static::assertInstanceOf(\JsonSerializable::class, $response);
    }

    public function testJsonSerialize()
    {
        $name = 'John Doe';
        $email = 'test@example.com';
        $roles = ['ROLE_TEST'];

        $expecterResult = [
            'name' => $name,
            'email' => $email,
            'roles' => $roles
        ];

        $user = $this->createMock(User::class);

        $user
            ->expects(static::once())
            ->method('getName')
            ->willReturn($name);

        $user
            ->expects(static::once())
            ->method('getEmail')
            ->willReturn($email);

        $user
            ->expects(static::once())
            ->method('getRoles')
            ->willReturn($roles);

        $response = new MyAccountResponse($user);

        static::assertEquals($expecterResult, $response->jsonSerialize());
    }
}

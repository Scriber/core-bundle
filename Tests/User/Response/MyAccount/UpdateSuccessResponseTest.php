<?php
namespace Scriber\Bundle\CoreBundle\Tests\User\Response\MyAccount;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\User\Response\MyAccount\UpdateSuccessResponse;

class UpdateSuccessResponseTest extends TestCase
{
    public function testImplementsJsonSerializable()
    {
        $response = new UpdateSuccessResponse('');

        static::assertInstanceOf(\JsonSerializable::class, $response);
    }

    public function testJsonSerialize()
    {
        $token = 'test';

        $expectedResult = [
            'token' => $token,
        ];

        $response = new UpdateSuccessResponse($token);

        static::assertEquals($expectedResult, $response->jsonSerialize());
    }
}

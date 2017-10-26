<?php
namespace Scriber\Bundle\CoreBundle\Tests\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Validator\Constraints\UserEmailNotExists;

class UserEmailNotExistsTest extends TestCase
{
    public function testTarget()
    {
        $expectedTarget = 'class';

        $constraint = new UserEmailNotExists();

        static::assertEquals($expectedTarget, $constraint->getTargets());
    }
}

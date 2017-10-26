<?php
namespace Scriber\Bundle\CoreBundle\Tests\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Validator\Constraints\UserEmailExists;

class UserEmailExistsTest extends TestCase
{
    public function testTarget()
    {
        $expectedTarget = 'class';

        $constraint = new UserEmailExists();

        static::assertEquals($expectedTarget, $constraint->getTargets());
    }
}

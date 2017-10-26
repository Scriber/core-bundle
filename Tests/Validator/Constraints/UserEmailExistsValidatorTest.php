<?php
namespace Scriber\Bundle\CoreBundle\Tests\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Validator\Constraints\UserEmailExists;
use Scriber\Bundle\CoreBundle\Validator\Constraints\UserEmailExistsValidator;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class UserEmailExistsValidatorTest extends TestCase
{
    /**
     * @var UserManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $manager;

    public function setUp()
    {
        $this->manager = $this->createMock(UserManager::class);
    }

    public function tearDown()
    {
        $this->manager = null;
    }

    public function testValidateNoValue()
    {
        $validator = new UserEmailExistsValidator($this->manager);
        $constraint = new UserEmailExists();

        $this->manager
            ->expects(static::never())
            ->method('userExists');

        $validator->validate(null, $constraint);
    }

    public function testValidateNonObjectValue()
    {
        $validator = new UserEmailExistsValidator($this->manager);
        $constraint = new UserEmailExists();

        $this->expectException(UnexpectedTypeException::class);
        $validator->validate([], $constraint);
    }

    /**
     * @param $email
     * @param $value
     *
     * @dataProvider validateWithoutViolationProvider
     */
    public function testValidateWithoutViolation($email, $value)
    {
        $validator = new UserEmailExistsValidator($this->manager);
        $constraint = new UserEmailExists();

        $this->manager
            ->expects(static::once())
            ->method('userExists')
            ->with($email)
            ->willReturn(true);

        $validator->validate($value, $constraint);
    }

    public function validateWithoutViolationProvider()
    {
        $email = 'test@example.com';
        $attributeValue = new class {
            public $email;
        };
        $attributeValue->email = $email;

        $callbackValue = new class($email) {
            private $value;

            public function __construct($value)
            {
                $this->value = $value;
            }

            public function email() {
                return $this->value;
            }
        };

        return [
            [
                $email,
                $callbackValue
            ],
            [
                $email,
                $attributeValue
            ]
        ];
    }

    public function testBuildViolation()
    {
        $email = 'test@example.com';

        $context = $this->createMock(ExecutionContextInterface::class);

        $validator = new UserEmailExistsValidator($this->manager);
        $validator->initialize($context);

        $constraint = new UserEmailExists();

        $value = new class {
            public $email;
        };
        $value->email = $email;

        $this->manager
            ->method('userExists')
            ->willReturn(false);

        $violation = $this->createMock(ConstraintViolationBuilderInterface::class);
        $context
            ->expects(static::once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violation);

        $violation
            ->expects(static::once())
            ->method('setInvalidValue')
            ->with($email)
            ->willReturnSelf();

        $violation
            ->expects(static::once())
            ->method('atPath')
            ->with($constraint->path)
            ->willReturnSelf();

        $violation
            ->expects(static::once())
            ->method('setCode')
            ->with('scriber.baa23506-2837-426b-b684-f930e2e496ca')
            ->willReturnSelf();

        $violation
            ->expects(static::once())
            ->method('setParameter')
            ->with('{{ email }}', $email)
            ->willReturnSelf();

        $violation
            ->expects(static::once())
            ->method('addViolation');

        $validator->validate($value, $constraint);
    }
}

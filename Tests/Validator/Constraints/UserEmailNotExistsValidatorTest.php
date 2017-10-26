<?php
namespace Scriber\Bundle\CoreBundle\Tests\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\Validator\Constraints\UserEmailNotExists;
use Scriber\Bundle\CoreBundle\Validator\Constraints\UserEmailNotExistsValidator;
use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class UserEmailNotExistsValidatorTest extends TestCase
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
        $validator = new UserEmailNotExistsValidator($this->manager);
        $constraint = new UserEmailNotExists();

        $this->manager
            ->expects(static::never())
            ->method('userExists');

        $validator->validate(null, $constraint);
    }

    public function testValidateNonObjectValue()
    {
        $validator = new UserEmailNotExistsValidator($this->manager);
        $constraint = new UserEmailNotExists();

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
        $validator = new UserEmailNotExistsValidator($this->manager);
        $constraint = new UserEmailNotExists();

        $this->manager
            ->expects(static::once())
            ->method('userExists')
            ->with($email)
            ->willReturn(false);

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

    /**
     * @param $value
     *
     * @dataProvider validateWithOldEmailSameAsEmail
     */
    public function testValidateWithOldEmailSameAsEmail($value)
    {
        $validator = new UserEmailNotExistsValidator($this->manager);
        $constraint = new UserEmailNotExists([
            'oldEmail' => 'oldEmail'
        ]);

        $this->manager
            ->expects(static::never())
            ->method('userExists');

        $validator->validate($value, $constraint);
    }

    public function validateWithOldEmailSameAsEmail()
    {
        $email = 'test@example.com';
        $attributeValue = new class {
            public $email;
            public $oldEmail;
        };
        $attributeValue->email = $email;
        $attributeValue->oldEmail = $email;

        $callbackValue = new class($email) {
            private $value;

            public function __construct($value)
            {
                $this->value = $value;
            }

            public function email() {
                return $this->value;
            }

            public function oldEmail() {
                return $this->value;
            }
        };

        return [
            [$callbackValue],
            [$attributeValue],
        ];
    }

    public function testBuildViolation()
    {
        $email = 'test@example.com';

        $context = $this->createMock(ExecutionContextInterface::class);

        $validator = new UserEmailNotExistsValidator($this->manager);
        $validator->initialize($context);

        $constraint = new UserEmailNotExists();

        $value = new class {
            public $email;
        };
        $value->email = $email;

        $this->manager
            ->method('userExists')
            ->willReturn(true);

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
            ->with('scriber.8e6c16c9-07eb-481c-b08f-ec776c505f77')
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

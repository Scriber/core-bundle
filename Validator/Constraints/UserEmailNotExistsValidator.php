<?php
namespace Scriber\Bundle\CoreBundle\Validator\Constraints;

use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UserEmailNotExistsValidator extends ConstraintValidator
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     *
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        if (!is_object($value)) {
            throw new UnexpectedTypeException($value, 'object');
        }

        if (is_callable([$value, $constraint->email])) {
            $email = $value->{$constraint->email}();
        } else {
            $email = $value->{$constraint->email};
        }

        $oldEmail = null;
        if ($constraint->oldEmail) {
            if (is_callable([$value, $constraint->oldEmail])) {
                $oldEmail = $value->{$constraint->oldEmail}();
            } else {
                $oldEmail = $value->{$constraint->oldEmail};
            }
        }

        if ($oldEmail && $email === $oldEmail) {
            return;
        }

        if ($this->userManager->userExists($email)) {
            $this->context
                ->buildViolation($constraint->message)
                ->setInvalidValue($email)
                ->atPath($constraint->path)
                ->setCode(UserEmailNotExists::EMAIL_EXISTS_ERROR)
                ->setParameter('{{ email }}', $email)
                ->addViolation();
        }
    }
}

<?php
namespace Scriber\Bundle\CoreBundle\Validator\Constraints;

use Scriber\Bundle\CoreBundle\User\UserManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UserEmailExistsValidator extends ConstraintValidator
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

        if (!$this->userManager->userExists($email)) {
            $this->context
                ->buildViolation($constraint->message)
                ->setInvalidValue($email)
                ->atPath($constraint->path)
                ->setCode(UserEmailExists::EMAIL_NOT_EXISTS_ERROR)
                ->setParameter('{{ email }}', $email)
                ->addViolation();
        }
    }
}

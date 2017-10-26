<?php
namespace Scriber\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserEmailNotExists extends Constraint
{
    const EMAIL_EXISTS_ERROR = 'scriber.8e6c16c9-07eb-481c-b08f-ec776c505f77';

    protected static $errorNames = array(
        self::EMAIL_EXISTS_ERROR => 'EMAIL_EXISTS_ERROR',
    );

    public $message = 'This e-mail address already exists.';

    public $path = 'email';

    public $email = 'email';
    public $oldEmail = null;

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

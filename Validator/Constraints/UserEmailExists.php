<?php
namespace Scriber\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserEmailExists extends Constraint
{
    const EMAIL_NOT_EXISTS_ERROR = 'scriber.baa23506-2837-426b-b684-f930e2e496ca';

    protected static $errorNames = array(
        self::EMAIL_NOT_EXISTS_ERROR => 'EMAIL_NOT_EXISTS_ERROR',
    );

    public $message = 'This e-mail doesn\'t exist.';

    public $path = 'email';
    public $email = 'email';

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

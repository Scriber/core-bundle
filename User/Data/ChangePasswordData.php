<?php
namespace Scriber\Bundle\CoreBundle\User\Data;

use Rzeka\DataHandler\DataHydratableInterface;
use Rzeka\DataHandler\DataHydrationTrait;

class ChangePasswordData implements DataHydratableInterface
{
    use DataHydrationTrait;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $oldPassword;
}

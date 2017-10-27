<?php
namespace Scriber\Bundle\CoreBundle\User\Data;

use Rzeka\DataHandler\DataHydratableInterface;
use Rzeka\DataHandler\DataHydrationTrait;

class CreateData implements DataHydratableInterface
{
    use DataHydrationTrait;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $name;
}

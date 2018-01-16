<?php
namespace Scriber\Bundle\CoreBundle\Event\User;

use Scriber\Bundle\CoreBundle\User\Data\CreateData;
use Symfony\Component\EventDispatcher\Event;

class UserCreateBeforeEvent extends Event
{
    /**
     * @var CreateData
     */
    private $data;

    /**
     * @param CreateData $data
     */
    public function __construct(CreateData $data)
    {
        $this->data = $data;
    }

    /**
     * @return CreateData
     */
    public function getData(): CreateData
    {
        return $this->data;
    }
}

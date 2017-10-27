<?php
namespace Scriber\Bundle\CoreBundle\Tests\User\Data;

use PHPUnit\Framework\TestCase;
use Rzeka\DataHandler\DataHydratableInterface;
use Scriber\Bundle\CoreBundle\Entity\User;
use Scriber\Bundle\CoreBundle\User\Data\UpdateData;

class CreateDataTest extends TestCase
{
    public function testImplementsHydratable()
    {
        $user = $this->createMock(User::class);
        $data = new UpdateData($user);

        static::assertInstanceOf(DataHydratableInterface::class, $data);
    }

    /**
     * @dataProvider propertiesProvider
     */
    public function testProperties($property)
    {
        $user = $this->createMock(User::class);
        $data = new UpdateData($user);

        static::assertTrue(property_exists($data, $property));
    }

    public function propertiesProvider()
    {
        return [
            ['email'],
            ['name']
        ];
    }
}

<?php
namespace Scriber\Bundle\CoreBundle\Tests\User\Data;

use PHPUnit\Framework\TestCase;
use Rzeka\DataHandler\DataHydratableInterface;
use Scriber\Bundle\CoreBundle\User\Data\ChangePasswordData;

class ChangePasswordDataTest extends TestCase
{
    public function testImplementsHydratable()
    {
        $data = new ChangePasswordData();

        static::assertInstanceOf(DataHydratableInterface::class, $data);
    }

    /**
     * @dataProvider propertiesProvider
     */
    public function testProperties($property)
    {
        $data = new ChangePasswordData();

        static::assertTrue(property_exists($data, $property));
    }

    public function propertiesProvider()
    {
        return [
            ['password'],
            ['oldPassword']
        ];
    }
}

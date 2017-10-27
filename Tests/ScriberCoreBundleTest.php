<?php
namespace Scriber\Bundle\CoreBundle\Tests;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\ScriberCoreBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ScriberCoreBundleTest extends TestCase
{
    public function testInstanceOfBundle()
    {
        $bundle = new ScriberCoreBundle();

        static::assertInstanceOf(Bundle::class, $bundle);
    }
}

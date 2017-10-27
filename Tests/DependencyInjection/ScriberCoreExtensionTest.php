<?php
namespace Scriber\Bundle\CoreBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\DependencyInjection\ScriberCoreExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ScriberCoreExtensionTest extends TestCase
{
    public function testConfigurationLoading(): void
    {
        $filesToLoad = [
            'services.xml',
            'users.xml',
            'controllers.xml'
        ];

        $filesToLoadCallbacks = array_map(function ($file) {
            return static::callback(function ($v) use ($file) { return $this->callbackEndsWith($file, $v); });
        }, $filesToLoad);

        $container = $this->createMock(ContainerBuilder::class);

        $container
            ->expects(static::atLeastOnce())
            ->method('fileExists')
            ->withConsecutive(...$filesToLoadCallbacks);

        $extension = new ScriberCoreExtension();
        $extension->load(
            [],
            $container
        );
    }

    private function callbackEndsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        if ($length === 0) {
            return false;
        }

        return (substr($haystack, -$length) === $needle);
    }
}

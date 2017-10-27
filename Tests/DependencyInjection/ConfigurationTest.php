<?php
namespace Scriber\Bundle\CoreBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\CoreBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testGetConfigTreeBuilder()
    {
        $root = 'scriber_core';

        $configuration = new Configuration();

        $result = $configuration->getConfigTreeBuilder();
        $nameResult = $result->buildTree()->getName();

        static::assertInstanceOf(TreeBuilder::class, $result);
        static::assertEquals($root, $nameResult);
    }
}

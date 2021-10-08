<?php

/**
 * This file is part of the cash-in.
 */

namespace Kematjaya\ChartBundle\CompilerPass;

use Kematjaya\ChartBundle\Chart\AbstractChart;
use Kematjaya\ChartBundle\Builder\ChartBuilderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package App\CompilerPass
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ChartCompilerPass implements CompilerPassInterface 
{
    public function process(ContainerBuilder $container) 
    {
        $definition = $container->findDefinition(ChartBuilderInterface::class);
        $taggedServices = $container->findTaggedServiceIds(AbstractChart::TAG_NAME);
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addChart', [new Reference($id)]);
        }
    }
}

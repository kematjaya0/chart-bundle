<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\CompilerPass;

use Kematjaya\ChartBundle\Chart\ChartRendererInterface;
use Kematjaya\ChartBundle\Builder\ChartRendererBuilderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Description of ChartRendererCompilerPass
 *
 * @author guest
 */
class ChartRendererCompilerPass implements CompilerPassInterface
{
    
    public function process(ContainerBuilder $container) 
    {
        $definition = $container->findDefinition(ChartRendererBuilderInterface::class);
        $taggedServices = $container->findTaggedServiceIds(ChartRendererInterface::TAG_NAME);
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addChartRenderer', [new Reference($id)]);
        }
    }

}

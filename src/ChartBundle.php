<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle;

use Kematjaya\ChartBundle\Chart\ChartRendererInterface;
use Kematjaya\ChartBundle\Chart\AbstractChart;
use Kematjaya\ChartBundle\CompilerPass\ChartRendererCompilerPass;
use Kematjaya\ChartBundle\CompilerPass\ChartCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Description of ChartBundle
 *
 * @author guest
 */
class ChartBundle extends Bundle
{
    public function build(ContainerBuilder $container) 
    {
        $container->registerForAutoconfiguration(AbstractChart::class)
                ->addTag(AbstractChart::TAG_NAME);
        $container->registerForAutoconfiguration(ChartRendererInterface::class)
                ->addTag(ChartRendererInterface::TAG_NAME);
        
        $container->addCompilerPass(new ChartCompilerPass());
        $container->addCompilerPass(new ChartRendererCompilerPass());
        
        parent::build($container);
    }
}

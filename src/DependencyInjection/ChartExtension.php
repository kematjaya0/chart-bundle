<?php

/**
 * This file is part of the step-bundle.
 */

namespace Kematjaya\ChartBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * @package Kematjaya\StepBundle\DependencyInjection
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ChartExtension extends Extension
{
    
    public function load(array $configs, ContainerBuilder $container) 
    {
        $loader = new YamlFileLoader(
            $container, 
            new FileLocator(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Resources/config')
        );
        $loader->load('services.yaml');
    }

}

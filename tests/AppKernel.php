<?php

namespace Kematjaya\ChartBundle\Tests;

use Kematjaya\ChartBundle\ChartBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class AppKernel extends Kernel 
{
    public function registerBundles()
    {
        return [
            new ChartBundle(),
            new TwigBundle(),
            new SecurityBundle(),
            new FrameworkBundle()
        ];
    }
    
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) use ($loader) 
        {
            $loader->load(__DIR__ . DIRECTORY_SEPARATOR . 'config/config.yml');
            $loader->load(__DIR__ . DIRECTORY_SEPARATOR . 'config/services_test.yml');
            $loader->load(__DIR__ . DIRECTORY_SEPARATOR . 'config/bundle.yml');
            
            $container->addObjectResource($this);
        });
    }
}

<?php

namespace Kematjaya\ChartBundle\Tests;

use Kematjaya\ChartBundle\Compiler\HighChartDataCompiler;
use Kematjaya\ChartBundle\Compiler\ChartDataCompilerInterface;
use Kematjaya\ChartBundle\Tests\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class BundleTest extends WebTestCase 
{
    public function testInitBundle(): ContainerInterface
    {
        $container = static::getContainer();
        
        $this->assertInstanceOf(ContainerInterface::class, $container);
        
        return $container;
    }
    
    public function testChartCompiler()
    {
        $container = static::getContainer();
        $this->assertTrue($container->has(ChartDataCompilerInterface::class));
        $compiler = $container->get(ChartDataCompilerInterface::class);
        $this->assertTrue($compiler instanceof HighChartDataCompiler);
        $charts = $compiler->compileChart([], []);
        $this->assertEquals(1, $charts->count());
    }
    
    public static function getKernelClass() 
    {
        return AppKernel::class;
    }
}

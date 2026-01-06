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
        $client = static::createClient();
        $container = $client->getContainer();

        $this->assertInstanceOf(ContainerInterface::class, $container);

        return $container;
    }
    
    public function testChartCompiler()
    {
        $container = static::getContainer();
        $this->assertTrue($container->has(ChartDataCompilerInterface::class));
        $compiler = $container->get(ChartDataCompilerInterface::class);
        $this->assertTrue($compiler instanceof HighChartDataCompiler);
        $charts1 = $compiler->compileChart([], []);
        $this->assertEquals(1, $charts1->count());
        $charts2 = $compiler->compileChart([], ["test"]);
        $this->assertEquals(1, $charts2->count());
        $charts3 = $compiler->compileChart([], ["dummy"]);
        $this->assertTrue($charts3->isEmpty());
    }
    
    public static function getKernelClass() :string
    {
        return AppKernel::class;
    }
}

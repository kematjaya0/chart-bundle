<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Builder;

use Kematjaya\ChartBundle\Chart\AbstractChart;
use Kematjaya\ChartBundle\Chart\ChartRendererInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of ChartRendererBuilder
 *
 * @author guest
 */
class ChartRendererBuilder implements ChartRendererBuilderInterface
{
    /**
     * 
     * @var Collection
     */
    private $elements;
    
    public function __construct() 
    {
        $this->elements = new ArrayCollection();
    }
    
    public function addChartRenderer(ChartRendererInterface $element): ChartRendererBuilderInterface 
    {
        if (!$this->elements->contains($element)) {
            $this->elements->add($element);
        }
        
        return $this;
    }

    public function getChartRenderer(AbstractChart $chart): ChartRendererInterface 
    {
        $elements = $this->elements->filter(function (ChartRendererInterface $element) use ($chart) {
            
            return $element->isSupported($chart);
        });
        
        if ($elements->isEmpty()) {
            
            throw new \Exception("doesn't support for '%s' class", get_class($chart));
        }
        
        return $elements->first();
    }

    public function getChartRenderers(): Collection 
    {
        return $this->elements;
    }

}

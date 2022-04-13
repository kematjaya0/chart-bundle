<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Builder;

use Kematjaya\ChartBundle\Chart\ShorteredChartInterface;
use Kematjaya\ChartBundle\Chart\AbstractChart;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of ChartBuilder
 *
 * @author guest
 */
class ChartBuilder implements ChartBuilderInterface
{
    /**
     * 
     * @var Collection
     */
    private $charts;
    
    public function __construct() 
    {
        $this->charts = new ArrayCollection();
    }
    
    public function addChart(AbstractChart $element): ChartBuilderInterface 
    {
        if (!$this->charts->contains($element)) {
            $this->charts->add($element);
        }
        
        return $this;
    }

    public function getChart(string $role): Collection 
    {
        return $this->getCharts()->filter(function (AbstractChart $chart) use ($role) {
            if (empty($chart->getRoles())) {
                
                return $chart;
            }
            
            return in_array($role, $chart->getRoles());
        });
    }

    public function getCharts(): Collection 
    {
        $iterator = $this->charts->getIterator();
        $iterator->uasort(function (AbstractChart $a, AbstractChart $b) {
            if ($a instanceof ShorteredChartInterface) {
                
                return $a->getSequence() > $b->getSequence() ? 1 : -1;
            }
            
            return $a->getTitle() > $b->getTitle() ? 1 : -1;
        });
        
        return new ArrayCollection(iterator_to_array($iterator));
    }

}

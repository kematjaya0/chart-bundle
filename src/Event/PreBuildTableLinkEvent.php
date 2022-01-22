<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Event;

use Kematjaya\ChartBundle\Chart\AbstractChart;
use Symfony\Contracts\EventDispatcher\Event;
use Doctrine\ORM\QueryBuilder;

/**
 * Description of PreBuildTableLinkEvent
 *
 * @author apple
 */
class PreBuildTableLinkEvent extends Event 
{
    /**
     * 
     * @var QueryBuilder
     */
    private $queryBuilder;
    
    /**
     * 
     * @var AbstractChart
     */
    private $chart;
    
    /**
     * 
     * @var string
     */
    private $value;
    
    const EVENT_NAME = 'chart.pre_build_table_link_event';
    
    public function __construct(QueryBuilder $queryBuilder, AbstractChart $chart, string $value) 
    {
        $this->queryBuilder = $queryBuilder;
        $this->chart = $chart;
        $this->value = $value;
    }
    
    public function getQueryBuilder(): QueryBuilder 
    {
        return $this->queryBuilder;
    }

    public function getChart(): AbstractChart 
    {
        return $this->chart;
    }

    public function getValue(): string 
    {
        return $this->value;
    }

    public function setChart(AbstractChart $chart):self 
    {
        $this->chart = $chart;
        
        return $this;
    }

    public function setValue(string $value):self 
    {
        $this->value = $value;
        
        return $this;
    }



}

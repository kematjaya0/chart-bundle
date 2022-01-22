<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Event;

use Kematjaya\ChartBundle\Chart\ClickableChartInterface;
use Symfony\Contracts\EventDispatcher\Event; 
use Doctrine\ORM\QueryBuilder;

/**
 * Description of ChartPointClickCreatedEvent
 *
 * @author apple
 */
class ChartPointClickCreatedEvent extends Event 
{
    /**
     * 
     * @var ClickableChartInterface
     */
    private $chart;
    
    /**
     * 
     * @var QueryBuilder
     */
    private $queryBuilder;
    
    /**
     * 
     * @var string
     */
    private $value;
    
    const EVENT_NAME = 'chart.point_click_created_event';
    
    public function __construct(ClickableChartInterface $chart, QueryBuilder $queryBuilder, string $value) 
    {
        $this->chart = $chart;
        $this->queryBuilder = $queryBuilder;
        $this->value = $value;
    }
    
    public function getChart(): ClickableChartInterface 
    {
        return $this->chart;
    }

    public function getQueryBuilder(): QueryBuilder 
    {
        return $this->queryBuilder;
    }

    public function getValue(): string 
    {
        return $this->value;
    }


    public function setValue(string $value):self 
    {
        $this->value = $value;
        
        return $this;
    }


}


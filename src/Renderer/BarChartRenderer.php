<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Renderer;

use Kematjaya\ChartBundle\Chart\AbstractChart;
use Doctrine\ORM\QueryBuilder;

/**
 * Description of BarChartRenderer
 *
 * @author guest
 */
class BarChartRenderer extends ColumnChartRenderer 
{
    public function isSupported(AbstractChart $chart): bool 
    {
        return AbstractChart::CHART_BAR === $chart->getChartType();
    }
    
    /**
     * 
     * @param AbstractChart $chart
     * @param QueryBuilder $qb
     * @return array array of highchart json
     */
    public function toArray(AbstractChart $chart, QueryBuilder $qb): array 
    {
        $arr = parent::toArray($chart, $qb);
        $arr['legend'] = [
            "layout" => 'vertical',
            "align" => 'right',
            "verticalAlign" => 'top',
            "x" => -40,
            "y" => 80,
            "floating" => true,
            "borderWidth" => 1,
            "shadow" => true
        ];
        $arr['plotOptions'] = [
            AbstractChart::CHART_BAR => [
                'dataLabels' => [
                    'enabled' => true
                ]
            ]
        ];
        
        return $arr;
    }
}

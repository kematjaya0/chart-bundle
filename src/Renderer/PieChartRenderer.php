<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Renderer;

use Kematjaya\ChartBundle\Chart\AbstractChart;
use Doctrine\ORM\QueryBuilder;

/**
 * Description of PieChartRenderer
 *
 * @author guest
 */
class PieChartRenderer extends AbstractChartRenderer
{
    public function isSupported(AbstractChart $chart): bool 
    {
        return AbstractChart::CHART_PIE === $chart->getChartType();
    }

    public function toArray(AbstractChart $chart, QueryBuilder $qb): array 
    {
        return [
            "chart" => [
                "plotBackgroundColor" => null,
                "plotBorderWidth" => null,
                "plotShadow" => false,
                "type" => $chart->getChartType()
            ]   
        ];
    }

}

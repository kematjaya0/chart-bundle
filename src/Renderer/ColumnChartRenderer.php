<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Renderer;

use Kematjaya\ChartBundle\Chart\AbstractChart;
use Doctrine\ORM\QueryBuilder;

/**
 * Description of ColumnChartRenderer
 *
 * @author guest
 */
class ColumnChartRenderer extends AbstractChartRenderer
{
    public function isSupported(AbstractChart $chart): bool 
    {
        return AbstractChart::CHART_COLUMN === $chart->getChartType();
    }

    public function toArray(AbstractChart $chart, QueryBuilder $qb): array 
    {
        return [
            "chart" => [
                "type" => $chart->getChartType()
            ],
            "xAxis" => [
                "categories" => $chart->getCategories(),
                "crosshair" => true
            ],
            "yAxis" => [
                "min" => 0,
                "title" => [
                    "text" => ''
                ]
            ],
            "tooltip" => [
                "headerFormat" => '<span style="font-size:10px">{point.key}</span><table>',
                "pointFormat" => '<tr><td style="color:{series.color};padding:0">{series.name}: </td>'
                    . '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
                "footerFormat" => '</table>',
                "shared" => true,
                "useHTML" => true
            ],
            "plotOptions" => [
                "column" => [
                    "pointPadding" => 0.2,
                    "borderWidth" => 0
                ]
            ]
        ];
            
    }

}

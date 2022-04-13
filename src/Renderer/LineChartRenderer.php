<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Renderer;

use Kematjaya\ChartBundle\Chart\AbstractChart;
use Doctrine\ORM\QueryBuilder;

/**
 * Description of LineChartRenderer
 *
 * @author guest
 */
class LineChartRenderer extends AbstractChartRenderer
{
    
    protected function toArray(AbstractChart $chart, QueryBuilder $qb): array 
    {
        return [
            "yAxis" => [
                "title" => [
                    "text" => $chart->getTitle()
                ]
            ],
            "xAxis" => [
                "accessibility" => [
                    "rangeDescription" => ''
                ],
                "categories" => $chart->getCategories()
            ],
            "legend" => [
                "layout" => 'vertical',
                "align" => 'right',
                "verticalAlign" => 'middle'
            ],
            "plotOptions" => [
                "series" => [
                    "label" => [
                        "connectorAllowed" => false
                    ]
                ]
            ],
            "responsive" => [
                "rules" => [
                    [
                        "condition" => [
                            "maxWidth" => 500
                        ],
                        "chartOptions" => [
                            "legend" => [
                                "layout" => 'horizontal',
                                "align" => 'center',
                                "verticalAlign" => 'bottom'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function isSupported(AbstractChart $chart): bool 
    {
        return AbstractChart::CHART_LINE === $chart->getChartType();
    }

}

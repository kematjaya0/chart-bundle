<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Renderer;

use Doctrine\ORM\QueryBuilder;
use Kematjaya\ChartBundle\Chart\AbstractChart;
use Kematjaya\ChartBundle\Chart\ClickableChartInterface;

/**
 * Description of AbstractChartRenderer
 *
 * @author guest
 */
abstract class AbstractChartRenderer implements ChartRendererInterface
{
    public function render(AbstractChart $chart, QueryBuilder $queryBuilder): array
    {
        $series = $chart->getSeries($queryBuilder);
        if ($chart instanceof ClickableChartInterface) {
            foreach ($series as $k => $v) {
                $series[$k]['point'] = [
                    "events" => [
                        "click" => '%func%',
                    ],
                ];
            }
        }

        $chartArray = [
            "title" => [
                "text" => $chart->getTitle(),
            ],
            "subtitle" => [
                "text" => '',
            ],
            "series" => $series,
            "credits" => [
                "enabled" => false,
            ],
        ];

        return array_merge($chartArray, $this->toArray($chart, $queryBuilder));
    }

    abstract protected function toArray(AbstractChart $chart, QueryBuilder $qb): array;
}

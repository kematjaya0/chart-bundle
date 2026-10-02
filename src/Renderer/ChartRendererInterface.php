<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Kematjaya\ChartBundle\Renderer;

use Doctrine\ORM\QueryBuilder;
use Kematjaya\ChartBundle\Chart\AbstractChart;

/**
 * @author guest
 */
interface ChartRendererInterface
{
    public const TAG_NAME = 'kematjaya.chart_renderer';

    public function render(AbstractChart $chart, QueryBuilder $qb): array;

    public function isSupported(AbstractChart $chart): bool;
}

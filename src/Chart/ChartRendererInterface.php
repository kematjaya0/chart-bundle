<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Kematjaya\ChartBundle\Chart;

use Doctrine\ORM\QueryBuilder;

/**
 *
 * @author guest
 */
interface ChartRendererInterface 
{
    const TAG_NAME = 'kematjaya.chart_renderer';
    
    public function render(AbstractChart $chart, QueryBuilder $qb):array;
    
    public function isSupported(AbstractChart $chart):bool;
}

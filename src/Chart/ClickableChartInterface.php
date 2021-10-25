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
interface ClickableChartInterface 
{
    public function getURL(QueryBuilder $queryBuilder):string;
    
    public function getModalDOMId():?string;
}

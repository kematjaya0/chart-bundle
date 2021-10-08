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
interface SummaryTableRepositoryInterface 
{
    public function getHeaders():array;
    
    public function getDatas(QueryBuilder $qb):array;
}

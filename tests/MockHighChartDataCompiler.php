<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Tests;

/**
 * Description of MockHighChartDataCompiler
 *
 * @author guest
 */
class MockHighChartDataCompiler extends \Kematjaya\ChartBundle\Compiler\HighChartDataCompiler 
{
    protected function getSingleRole():?string
    {
        return "ROLE_TEST";
    }
}

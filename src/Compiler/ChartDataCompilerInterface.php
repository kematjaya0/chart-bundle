<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Kematjaya\ChartBundle\Compiler;

use Doctrine\Common\Collections\Collection;

/**
 *
 * @author guest
 */
interface ChartDataCompilerInterface 
{
    public function getStylesheetPath():?string;
    
    public function getJavascriptPath():?string;
    
    public function compileChart(array $options = [], array $group = []):Collection;
}

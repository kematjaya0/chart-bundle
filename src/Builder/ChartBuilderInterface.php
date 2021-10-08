<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Kematjaya\ChartBundle\Builder;

use Kematjaya\ChartBundle\Chart\AbstractChart;
use Doctrine\Common\Collections\Collection;

/**
 *
 * @author guest
 */
interface ChartBuilderInterface 
{
    public function addChart(AbstractChart $element): self;

    public function getChart(string $role): Collection;

    public function getCharts(): Collection;
}

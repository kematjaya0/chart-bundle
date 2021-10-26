<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Kematjaya\ChartBundle\Builder;

use Kematjaya\ChartBundle\Chart\AbstractChart;
use Kematjaya\ChartBundle\Renderer\ChartRendererInterface;
use Doctrine\Common\Collections\Collection;

/**
 *
 * @author guest
 */
interface ChartRendererBuilderInterface 
{
    public function addChartRenderer(ChartRendererInterface $element): self;

    public function getChartRenderer(AbstractChart $chart): ChartRendererInterface;

    public function getChartRenderers(): Collection;
}

<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Builder;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Kematjaya\ChartBundle\Chart\AbstractChart;
use Kematjaya\ChartBundle\Renderer\ChartRendererInterface;

/**
 * Description of ChartRendererBuilder
 *
 * @author guest
 */
class ChartRendererBuilder implements ChartRendererBuilderInterface
{
    private readonly ArrayCollection $elements;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }

    public function addChartRenderer(ChartRendererInterface $element): ChartRendererBuilderInterface
    {
        if (!$this->elements->contains($element)) {
            $this->elements->add($element);
        }

        return $this;
    }

    public function getChartRenderer(AbstractChart $chart): ChartRendererInterface
    {
        $elements = $this->elements->filter(fn(ChartRendererInterface $element): bool => $element->isSupported($chart));

        if ($elements->isEmpty()) {

            throw new \Exception(sprintf("doesn't support for '%s' class", $chart::class));
        }

        return $elements->first();
    }

    public function getChartRenderers(): Collection
    {
        return $this->elements;
    }

}

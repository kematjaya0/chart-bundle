<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Builder;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Kematjaya\ChartBundle\Chart\AbstractChart;
use Kematjaya\ChartBundle\Chart\ShorteredChartInterface;

/**
 * Description of ChartBuilder
 *
 * @author guest
 */
class ChartBuilder implements ChartBuilderInterface
{
    private readonly ArrayCollection $charts;

    public function __construct()
    {
        $this->charts = new ArrayCollection();
    }

    public function addChart(AbstractChart $element): ChartBuilderInterface
    {
        if (!$this->charts->contains($element)) {
            $this->charts->add($element);
        }

        return $this;
    }

    public function getChart(string $role): Collection
    {
        return $this->getCharts()->filter(function (AbstractChart $chart) use ($role): AbstractChart|bool {
            if (empty($chart->getRoles())) {

                return $chart;
            }

            return in_array($role, $chart->getRoles());
        });
    }

    public function getCharts(): Collection
    {
        $shorts = $this->charts->filter(fn(AbstractChart $chart): bool => $chart instanceof ShorteredChartInterface);
        $nonShort = $this->charts->filter(fn(AbstractChart $chart): bool => !$chart instanceof ShorteredChartInterface);

        $data = array_merge(iterator_to_array($this->short($shorts, fn(AbstractChart $a, AbstractChart $b): int => $a->getSequence() > $b->getSequence() ? 1 : -1)), $nonShort->toArray());

        return new ArrayCollection($data);
    }

    protected function short(Collection $charts, callable $callback): \Traversable
    {
        $iterator = $charts->getIterator();
        $iterator->uasort(fn(AbstractChart $a, AbstractChart $b) => call_user_func($callback, $a, $b));

        return $iterator;
    }
}

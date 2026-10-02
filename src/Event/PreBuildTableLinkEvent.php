<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Event;

use Doctrine\ORM\QueryBuilder;
use Kematjaya\ChartBundle\Chart\AbstractChart;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Description of PreBuildTableLinkEvent
 *
 * @author apple
 */
class PreBuildTableLinkEvent extends Event
{
    public const EVENT_NAME = 'chart.pre_build_table_link_event';

    public function __construct(
        private readonly QueryBuilder $queryBuilder,
        private AbstractChart $chart,
        private string $value,
    ) {}

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function getChart(): AbstractChart
    {
        return $this->chart;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setChart(AbstractChart $chart): self
    {
        $this->chart = $chart;

        return $this;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }



}

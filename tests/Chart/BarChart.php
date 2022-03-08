<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Tests\Chart;

use Doctrine\ORM\QueryBuilder;
use Kematjaya\ChartBundle\Chart\AbstractChart;
use Kematjaya\ChartBundle\Chart\SummaryTableRepositoryInterface;
use Kematjaya\ChartBundle\Chart\ClickableChartInterface;

/**
 * Description of BarChart
 *
 * @author guest
 */
class BarChart extends AbstractChart implements SummaryTableRepositoryInterface, ClickableChartInterface 
{
    //put your code here
    public function getCategories(): array 
    {
        return [];
    }

    public function getChartTitle(): string 
    {
        return "test";
    }

    public function getQueryBuilder(string $alias = 't', array $params = []): QueryBuilder 
    {
        return new QueryBuilder($this->getEntityManager());
    }

    public function getSequence(): int 
    {
        return 1;
    }

    public function getSeries(QueryBuilder $qb): array 
    {
        return [];
    }

    public function getTitle(): string 
    {
        return $this->getChartTitle();
    }

    public function getDatas(QueryBuilder $qb): array 
    {
        return [
            [1], [2], [3]
        ];
    }

    public function getHeaders(): array 
    {
        return [
            "test"
        ];
    }

    public function getModalDOMId(): ?string 
    {
        return "#test";
    }

    public function getQueryKey(): ?string 
    {
        return null;
    }

    public function getURL(QueryBuilder $queryBuilder): string 
    {
        return "foo/bar";
    }

}

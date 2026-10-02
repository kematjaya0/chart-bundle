<?php

namespace Kematjaya\ChartBundle\Tests\Chart;

use Doctrine\ORM\QueryBuilder;
use Kematjaya\ChartBundle\Chart\AbstractChart;
use Kematjaya\ChartBundle\Chart\ClickableChartInterface;
use Kematjaya\ChartBundle\Chart\GroupChartInterface;
use Kematjaya\ChartBundle\Chart\ShorteredChartInterface;
use Kematjaya\ChartBundle\Chart\SummaryTableRepositoryInterface;

/**
 * Chart column dengan tabel & klik (grup "test")
 */
class BarChart extends AbstractChart implements GroupChartInterface, SummaryTableRepositoryInterface, ClickableChartInterface, ShorteredChartInterface
{
    public function getCategories(): array
    {
        return ['Jan', 'Feb'];
    }

    public function getQueryBuilder(string $alias = 't', array $params = []): QueryBuilder
    {
        return new QueryBuilder($this->getEntityManager());
    }

    public function getSequence(): int
    {
        return 2;
    }

    public function getSeries(QueryBuilder $qb): array
    {
        return [['name' => 'total', 'data' => [1, 2]]];
    }

    public function getTitle(): string
    {
        return "Bar test";
    }

    public function getDatas(QueryBuilder $qb): array
    {
        return [
            ['label' => 'Toko A&B', 'total' => 1],
            ['label' => null, 'total' => 2],
        ];
    }

    public function getHeaders(): array
    {
        return ["label", "total"];
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

    public static function getGroups(): array
    {
        return ["test"];
    }
}

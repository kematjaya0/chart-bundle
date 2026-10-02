<?php

namespace Kematjaya\ChartBundle\Tests\Chart;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Kematjaya\ChartBundle\Chart\AbstractChart;
use Kematjaya\ChartBundle\Chart\ShorteredChartInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Chart pie urutan pertama, tanpa grup
 */
class PieChart extends AbstractChart implements ShorteredChartInterface
{
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $entityManager)
    {
        parent::__construct($translator, $entityManager);

        $this->setChartType(self::CHART_PIE)->setWidth(6);
    }

    public function getCategories(): array
    {
        return [];
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
        return [['name' => 'share', 'data' => [['name' => 'a', 'y' => 1]]]];
    }

    public function getTitle(): string
    {
        return "Pie test";
    }
}

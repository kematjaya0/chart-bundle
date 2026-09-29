<?php

namespace Kematjaya\ChartBundle\Tests\Chart;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Kematjaya\ChartBundle\Chart\AbstractChart;
use Kematjaya\ChartBundle\Chart\ClickableChartInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Chart line khusus ROLE_ADMIN, klik tanpa modal
 */
class AdminLineChart extends AbstractChart implements ClickableChartInterface
{
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $entityManager)
    {
        parent::__construct($translator, $entityManager);

        $this->setChartType(self::CHART_LINE);
    }

    public function getRoles(): array
    {
        return ['ROLE_ADMIN'];
    }

    public function getCategories(): array
    {
        return ['a'];
    }

    public function getQueryBuilder(string $alias = 't', array $params = []): QueryBuilder
    {
        return new QueryBuilder($this->getEntityManager());
    }

    public function getSeries(QueryBuilder $qb): array
    {
        return [['name' => 'line', 'data' => [1]]];
    }

    public function getTitle(): string
    {
        return "Admin line";
    }

    public function getURL(QueryBuilder $queryBuilder): string
    {
        return "/admin/detail";
    }

    public function getModalDOMId(): ?string
    {
        return null;
    }

    public function getQueryKey(): ?string
    {
        return 'kategori';
    }
}

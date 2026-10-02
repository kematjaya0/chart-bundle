<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Chart;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Description of AbstractChart
 *
 * @author guest
 */
abstract class AbstractChart
{
    protected float $width;

    protected string $chartType;

    public const TAG_NAME = 'kematjaya.chart';

    public const CHART_COLUMN = 'column';
    public const CHART_LINE = 'line';
    public const CHART_PIE = 'pie';
    public const CHART_BAR = 'bar';


    public function __construct(
        protected readonly TranslatorInterface $translator,
        private readonly EntityManagerInterface $entityManager,
    ) {
        $this->width = 12;
        $this->chartType = self::CHART_COLUMN;
    }

    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function setWidth(float $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getChartType(): string
    {
        return $this->chartType;
    }

    public function setChartType(string $chartType): self
    {
        $this->chartType = $chartType;
        return $this;
    }

    public function getRoles(): array
    {
        return [

        ];
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function createQueryBuilder(string $className, string $alias = 't'): QueryBuilder
    {
        return $this->entityManager->getRepository($className)->createQueryBuilder($alias);
    }

    public function getYTitle(): string
    {
        return $this->translator->trans("total");
    }

    abstract public function getSeries(QueryBuilder $qb): array;

    abstract public function getQueryBuilder(string $alias = 't', array $params = []): QueryBuilder;

    abstract public function getTitle(): string;

    abstract public function getCategories(): array;
}

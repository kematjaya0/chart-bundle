<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Chart;

use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of AbstractChart
 *
 * @author guest
 */
abstract class AbstractChart 
{
    /**
     * 
     * @var TranslatorInterface
     */
    protected $translator;
    
    /**
     * 
     * @var float
     */
    protected $width;
    
    /**
     * 
     * @var string
     */
    protected $chartType;
    
    /**
     * 
     * @var EntityManagerInterface
     */
    private $entityManager;
    
    const TAG_NAME = 'kematjaya.chart';
    
    const CHART_COLUMN = 'column';
    const CHART_LINE = 'line';
    const CHART_PIE = 'pie';
    
    
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $entityManager) 
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
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

    public function setWidth(float $width):self 
    {
        $this->width = $width;
        
        return $this;
    }
    
    public function getChartType(): string 
    {
        return $this->chartType;
    }

    public function setChartType(string $chartType):self  
    {
        $this->chartType = $chartType;
        return $this;
    }

    public function getRoles():array
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
    
    abstract public function getSeries(QueryBuilder $qb):array;
    
    abstract public function getQueryBuilder(string $alias = 't', array $params = []): QueryBuilder;
    
    abstract public function getTitle():string;
    
    abstract public function getCategories():array;
    
    abstract public function getChartTitle():string;
    
    abstract public function getSequence():int;
}

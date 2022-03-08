<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Compiler;

use Kematjaya\ChartBundle\Chart\ClickableChartInterface;
use Kematjaya\ChartBundle\Chart\SummaryTableRepositoryInterface;
use Kematjaya\ChartBundle\Builder\ChartRendererBuilderInterface;
use Kematjaya\ChartBundle\Chart\AbstractChart;
use Kematjaya\ChartBundle\Builder\ChartBuilderInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Kematjaya\UserBundle\Entity\DefaultUser;

/**
 * Description of HighChartDataCompiler
 *
 * @author guest
 */
class HighChartDataCompiler implements ChartDataCompilerInterface
{
    /**
     * 
     * @var ChartBuilderInterface
     */
    private $chartBuilder;
    
    /**
     * 
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    
    /**
     * 
     * @var ChartRendererBuilderInterface
     */
    private $chartRendererBuilder;
    
    public function __construct(ChartBuilderInterface $chartBuilder, ChartRendererBuilderInterface $chartRendererBuilder, TokenStorageInterface $tokenStorage) 
    {
        $this->chartRendererBuilder = $chartRendererBuilder;
        $this->chartBuilder = $chartBuilder;
        $this->tokenStorage = $tokenStorage;
    }
    
    public function getJavascriptPath(): ?string 
    {
        return '@Chart/javascripts.twig';
    }

    public function getStylesheetPath(): ?string 
    {
        return '@Chart/stylesheets.twig';
    }
    
    public function compileChart(array $options = [], array $group = []): Collection 
    {
        $data = new ArrayCollection();
        $singleRole = $this->getSingleRole();
        if (null === $singleRole) {

            return $data;
        }
            
        foreach ($this->chartBuilder->getChart($singleRole) as $chart) {
            if (!$chart instanceof AbstractChart) {
                continue;
            }

            $chartRenderer = $this->chartRendererBuilder->getChartRenderer($chart);

            $id = md5(date('Y-m-d H:i:s') . rand());
            $qb = $chart->getQueryBuilder('t', isset($options['filter']) ? $options['filter'] : []);
            $graph = json_encode(
                $chartRenderer->render($chart, $qb)
            );
            
            $table = null;
            if ($chart instanceof SummaryTableRepositoryInterface) {
                $table = [
                    'header' => $chart->getHeaders(),
                    'data' => $this->buildTableData($chart, $qb)
                ];
            }

            $clickableLink = [];
            if ($chart instanceof ClickableChartInterface) {
                $clickableLink['"%func%"'] = $this->buildClickPoint($chart, $qb);
            }

            $data->offsetSet($id, [
                'title' => $chart->getTitle(),
                'id' => $id,
                'chart' => $graph,
                'table' => $table,
                'table_active' => $chart ? '' : 'active',
                'width' => $chart->getWidth(),
                'clickable' => $clickableLink
            ]);
        }
            
        return $data;
    }

    protected function buildTableData(SummaryTableRepositoryInterface $chart, QueryBuilder $queryBuilder):array
    {
        if (!$chart instanceof ClickableChartInterface) {
            
            return $chart->getDatas($queryBuilder);
        }
        
        return array_map(function (array $row) use ($chart, $queryBuilder) {
            $modalAttribute = [];
            if ($chart->getModalDOMId()) {
                $modalAttribute[] = 'data-toggle="modal"';
                $modalAttribute[] = sprintf('data-target="%s"', $chart->getModalDOMId());
            }
            
            $keys = array_keys($row);
            $label = $keys[0];
            $value = $keys[count($keys) - 1];
            $queryKey = null !== $chart->getQueryKey() ? $chart->getQueryKey() : 'q';

            $row[$value] = sprintf('<a href="%s?%s=%s" %s>%s</a>', $chart->getURL($queryBuilder), $queryKey, $row[$label], implode(" ", $modalAttribute), $row[$value]);
            
            return $row;
        }, $chart->getDatas($queryBuilder));
    }
    
    protected function buildClickPoint(ClickableChartInterface $chart, QueryBuilder $queryBuilder):string
    {
        $function = 'function (event) {
            let query = event.point.category;
            if (typeof query == "undefined") {
                query = event.point.name;
            }
            %s
        }';
        
        $queryKey = null !== $chart->getQueryKey() ? $chart->getQueryKey() : 'q';
        if (!$chart->getModalDOMId()) {
            $actions = sprintf('window.location.href = "%s?%s=" + query;', $chart->getURL($queryBuilder), $queryKey);
            
            return sprintf($function, $actions);
        }
          
        $actions = sprintf(''
                . '$("%s").modal("show");'
                . '$("%s").find(".modal-content").load("%s?%s=" + query);', 
                $chart->getModalDOMId(),
                $chart->getModalDOMId(),
                $chart->getURL($queryBuilder),
                $queryKey
            );
        
        return sprintf($function, $actions);
    }
    
    protected function getSingleRole():?string
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            
            return null;
        }
        
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            
            return null;
        }
        
        if ($user instanceof DefaultUser) {
            
            return $user->getSingleRole();
        }
        
        $roles = $user->getRoles();
        
        return end($roles);
    }

}

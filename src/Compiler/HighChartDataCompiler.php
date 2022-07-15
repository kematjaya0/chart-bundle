<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Compiler;

use Kematjaya\ChartBundle\Chart\AbstractChart;
use Kematjaya\ChartBundle\Chart\GroupChartInterface;
use Kematjaya\ChartBundle\Chart\ClickableChartInterface;
use Kematjaya\ChartBundle\Chart\SummaryTableRepositoryInterface;
use Kematjaya\ChartBundle\Builder\ChartRendererBuilderInterface;
use Kematjaya\ChartBundle\Builder\ChartBuilderInterface;
use Kematjaya\ChartBundle\Event\PreBuildTableLinkEvent;
use Kematjaya\ChartBundle\Event\ChartPointClickCreatedEvent;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
    
    /**
     * 
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    
    public function __construct(ChartBuilderInterface $chartBuilder, EventDispatcherInterface $eventDispatcher, ChartRendererBuilderInterface $chartRendererBuilder, TokenStorageInterface $tokenStorage) 
    {
        $this->chartRendererBuilder = $chartRendererBuilder;
        $this->chartBuilder = $chartBuilder;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
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
        $charts = (null !== $singleRole) ? $this->chartBuilder->getChart($singleRole) : $this->chartBuilder->getCharts();
            
        foreach ($charts as $chart) {
            
            if (!$this->isValidObject($chart, $group)) {
                continue;
            }
            
            $id = md5(date('Y-m-d H:i:s') . rand());

            $data->offsetSet($id, $this->render($id, $chart, $options));
        }
            
        return $data;
    }

    protected function render(string $id, AbstractChart $chart, array $options):array
    {
        $chartRenderer = $this->chartRendererBuilder->getChartRenderer($chart);

        
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
        
        return [
            'title' => $chart->getTitle(),
            'id' => $id,
            'chart' => $graph,
            'table' => $table,
            'table_active' => $chart ? '' : 'active',
            'width' => $chart->getWidth(),
            'clickable' => $clickableLink
        ];
    }
    
    protected function isValidObject($chart, array $groups = []):bool
    {
        if (!$chart instanceof AbstractChart) {
            return false;
        }

        if (!$chart instanceof GroupChartInterface) {
            
            return true;
        }
        
        if (empty($groups)) {
            
            return true;
        }
        
        $className = get_class($chart);
        $objectGroups = call_user_func([$className, 'getGroups']);
        $selectedGroups = array_filter($objectGroups, function ($group) use ($groups) {
            return in_array($group, $groups);
        });
        
        return !empty($selectedGroups);
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
            
            $event = $this->eventDispatcher->dispatch(
                new PreBuildTableLinkEvent($queryBuilder, $chart, $row[$label])
            );
            $row[$value] = sprintf('<a href="%s?%s=%s" %s>%s</a>', $chart->getURL($queryBuilder), $queryKey, $event->getValue(), implode(" ", $modalAttribute), $row[$value]);
            
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
            
            $event = $this->eventDispatcher->dispatch(
                new ChartPointClickCreatedEvent($chart, $queryBuilder, sprintf($function, $actions)),
                ChartPointClickCreatedEvent::EVENT_NAME
            );

            return $event->getValue();
        }
          
        $actions = sprintf(''
                . '$("%s").modal("show");'
                . '$("%s").find(".modal-content").load("%s?%s=" + query);', 
                $chart->getModalDOMId(),
                $chart->getModalDOMId(),
                $chart->getURL($queryBuilder),
                $queryKey
            );
        
        $event = $this->eventDispatcher->dispatch(
            new ChartPointClickCreatedEvent($chart, $queryBuilder, sprintf($function, $actions)),
            ChartPointClickCreatedEvent::EVENT_NAME
        );
        
        return $event->getValue();
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

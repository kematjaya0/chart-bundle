<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Twig;

use Kematjaya\ChartBundle\Event\PreBuildTableLinkEvent;
use Kematjaya\ChartBundle\Chart\ClickableChartInterface;
use Kematjaya\ChartBundle\Chart\SummaryTableRepositoryInterface;
use Kematjaya\ChartBundle\Chart\AbstractChart;
use Kematjaya\ChartBundle\Builder\ChartRendererBuilderInterface;
use Kematjaya\ChartBundle\Builder\ChartBuilderInterface;
use Kematjaya\UserBundle\Entity\DefaultUser;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;
use Doctrine\ORM\QueryBuilder;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Description of ChartExtension
 *
 * @author guest
 */
class ChartExtension extends AbstractExtension
{
    /**
     *
     * @var Environment
     */
    private $twig;
    
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
    
    public function __construct(Environment $twig, EventDispatcherInterface $eventDispatcher, ChartRendererBuilderInterface $chartRendererBuilder, TokenStorageInterface $tokenStorage, ChartBuilderInterface $chartBuilder) 
    {
        $this->chartRendererBuilder = $chartRendererBuilder;
        $this->tokenStorage = $tokenStorage;
        $this->chartBuilder = $chartBuilder;
        $this->twig = $twig;
        $this->eventDispatcher = $eventDispatcher;
    }
    
    public function getFunctions()
    {
        return [
            new TwigFunction('chart_stylesheet', [$this, 'renderCSS'], ['is_safe' => ['html']]),
            new TwigFunction('chart_javascript', [$this, 'renderJS'], ['is_safe' => ['html']]),
            new TwigFunction('render_chart', [$this, 'render'], ['is_safe' => ['html']])
        ];
    }
    
    public function renderCSS()
    {
        return $this->twig->render('@Chart/stylesheets.twig');
    }
    
    public function renderJS()
    {
        return $this->twig->render('@Chart/javascripts.twig');
    }
    
    public function render(array $options = []):?string
    {
        try {
            $singleRole = $this->getSingleRole();
            if (null === $singleRole) {

                return null;
            }
            
            $statistics = [];
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
                
                $statistics[$id] = [
                    'title' => $chart->getTitle(),
                    'id' => $id,
                    'chart' => $graph,
                    'table' => $table,
                    'table_active' => $chart ? '' : 'active',
                    'width' => $chart->getWidth(),
                    'clickable' => $clickableLink
                ];
            }   

            return $this->twig->render('@Chart/charts.twig', [
                'statistics' => $statistics
            ]);
        } catch (\Exception $ex) {
            
            return sprintf("'%s' line %s: %s", $ex->getFile(), $ex->getLine(), $ex->getMessage());
        }
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
                new PreBuildTableLinkEvent($queryBuilder, $chart, $row[$label]),
                PreBuildTableLinkEvent::EVENT_NAME
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

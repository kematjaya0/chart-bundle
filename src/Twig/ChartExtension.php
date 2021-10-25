<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Twig;

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
    
    public function __construct(Environment $twig, ChartRendererBuilderInterface $chartRendererBuilder, TokenStorageInterface $tokenStorage, ChartBuilderInterface $chartBuilder) 
    {
        $this->chartRendererBuilder = $chartRendererBuilder;
        $this->tokenStorage = $tokenStorage;
        $this->chartBuilder = $chartBuilder;
        $this->twig = $twig;
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
                    $this->buildChartData($chart, $qb, $chart->getChartType())
                );
                
                $table = null;
                if ($chart instanceof SummaryTableRepositoryInterface) {
                    $table = [
                        'header' => $chart->getHeaders(),
                        'data' => $chart->getDatas($qb)
                    ];
                }

                $clickableLink = [];
                if ($chart instanceof ClickableChartInterface) {
                    $clickableLink['"%func%"'] = $this->buildClickPoint($chart, $qb);
                    if (!empty($table)) {
                        $table['data'] = array_map(function (array $row) use ($chart, $qb) {
                            
                            $modalAttribute = [];
                            if ($chart->getModalDOMId()) {
                                $modalAttribute[] = 'data-toggle="modal"';
                                $modalAttribute[] = sprintf('data-target="%s"', $chart->getModalDOMId());
                            }
                            
                            $row['total'] = sprintf('<a href="%s" %s>%s</a>', $chart->getURL($qb), implode(" ", $modalAttribute), $row['total']);
                            
                            return $row;
                        }, $table['data']);
                    }
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
            dump(sprintf("'%s' line %s: %s", $ex->getFile(), $ex->getLine(), $ex->getMessage()));
            return '';
        }
    }
    
    protected function buildClickPoint(ClickableChartInterface $chart, QueryBuilder $queryBuilder):string
    {
        return sprintf('function (event) {
                let query = event.point.category;
                if (typeof query == "undefined") {
                    query = event.point.name;
                }
                window.location.href = "%s?q=" + event.point.category;
            }', $chart->getURL($queryBuilder));
    }
    
    protected function buildChartData(AbstractChart $repository, QueryBuilder $queryBuilder, string $chartType): array
    {
        $series = $repository->getSeries($queryBuilder);
        if ($repository instanceof ClickableChartInterface) {
            foreach ($series as $k => $v) {
                $series[$k]['point'] = [
                    "events" => [
                        "click" => '%func%'
                    ]
                ];
            }   
        }
        
        $chart = [
            "title" => [
                "text" => $repository->getChartTitle()
            ],
            "subtitle" => [
                "text" => ''
            ],
            "series" => $series,
            "credits" => [
                "enabled" => false
            ]
        ];
        
        if (AbstractChart::CHART_PIE === $chartType) {
            $chart["chart"] = [
                "plotBackgroundColor" => null,
                "plotBorderWidth" => null,
                "plotShadow" => false,
                "type" => $chartType
            ];
            
            return $chart;
        }
        
        if (AbstractChart::CHART_LINE === $chartType) {
            
            return array_merge($chart, [
                "yAxis" => [
                    "title" => [
                        "text" => $repository->getChartTitle()
                    ]
                ],
                "xAxis" => [
                    "accessibility" => [
                        "rangeDescription" => ''
                    ],
                    "categories" => $repository->getCategories()
                ],
                "legend" => [
                    "layout" => 'vertical',
                    "align" => 'right',
                    "verticalAlign" => 'middle'
                ],
                "plotOptions" => [
                    "series" => [
                        "label" => [
                            "connectorAllowed" => false
                        ]
                    ]
                ],
                "responsive" => [
                    "rules" => [
                        [
                            "condition" => [
                                "maxWidth" => 500
                            ],
                            "chartOptions" => [
                                "legend" => [
                                    "layout" => 'horizontal',
                                    "align" => 'center',
                                    "verticalAlign" => 'bottom'
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        }
        
        $chart["chart"] = [
            "type" => $chartType
        ];
        $chart["xAxis"] = [
            "categories" => $repository->getCategories(),
            "crosshair" => true
        ];
        $chart["yAxis"] = [
            "min" => 0,
            "title" => [
                "text" => ''
            ]
        ];
        $chart["tooltip"] = [
            "headerFormat" => '<span style="font-size:10px">{point.key}</span><table>',
            "pointFormat" => '<tr><td style="color:{series.color};padding:0">{series.name}: </td>'
                . '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
            "footerFormat" => '</table>',
            "shared" => true,
            "useHTML" => true
        ];
        $chart["plotOptions"] = [
            "column" => [
                "pointPadding" => 0.2,
                "borderWidth" => 0
            ]
        ];
        
        return $chart;
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

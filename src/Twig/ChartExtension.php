<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Twig;

use Kematjaya\ChartBundle\Compiler\ChartDataCompilerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;


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
     * @var ChartDataCompilerInterface
     */
    private $chartDataCompiler;
    
    const ONLY_CHART = 'only_chart';
    const DIV_ATTR = 'div_attr';
    
    public function __construct(Environment $twig, ChartDataCompilerInterface $chartDataCompiler) 
    {
        $this->chartDataCompiler = $chartDataCompiler;
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
    
    public function renderCSS():?string
    {
        $path = $this->chartDataCompiler->getStylesheetPath();
        if (null === $path) {
            
            return null;
        }
        
        return $this->twig->render($path);
    }
    
    public function renderJS():?string
    {
        $path = $this->chartDataCompiler->getJavascriptPath();
        if (null === $path) {
            
            return null;
        }
        
        return $this->twig->render($path);
    }
    
    public function render(array $options = [], array $groups = []):?string
    {
        $options[self::ONLY_CHART] = (isset($options[self::ONLY_CHART])) ? (bool) $options[self::ONLY_CHART] : false;
        $attributes = isset($options[self::DIV_ATTR]) ? $options[self::DIV_ATTR] : [];
        array_walk($attributes, function (&$value, $key) {
            $value = sprintf('%s="%s"', $key, $value);
        });
        try {
            return $this->twig->render('@Chart/charts.twig', [
                self::ONLY_CHART => $options[self::ONLY_CHART],
                self::DIV_ATTR => implode(" ", $attributes),
                'statistics' => $this->chartDataCompiler->compileChart($options, $groups)
            ]);
        } catch (\Exception $ex) {
            
            return sprintf("'%s' line %s: %s", $ex->getFile(), $ex->getLine(), $ex->getMessage());
        }
    }
}

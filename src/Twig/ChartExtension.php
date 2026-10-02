<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Twig;

use Kematjaya\ChartBundle\Compiler\ChartDataCompilerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Description of ChartExtension
 *
 * @author guest
 */
class ChartExtension extends AbstractExtension
{
    public const ONLY_CHART = 'only_chart';
    public const DIV_ATTR = 'div_attr';

    public function __construct(
        private readonly Environment $twig,
        private readonly ChartDataCompilerInterface $chartDataCompiler,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('chart_stylesheet', $this->renderCSS(...), ['is_safe' => ['html']]),
            new TwigFunction('chart_javascript', $this->renderJS(...), ['is_safe' => ['html']]),
            new TwigFunction('render_chart', $this->render(...), ['is_safe' => ['html']]),
        ];
    }

    public function renderCSS(): ?string
    {
        $path = $this->chartDataCompiler->getStylesheetPath();
        if (null === $path) {

            return null;
        }

        return $this->twig->render($path);
    }

    public function renderJS(): ?string
    {
        $path = $this->chartDataCompiler->getJavascriptPath();
        if (null === $path) {

            return null;
        }

        return $this->twig->render($path);
    }

    public function render(array $options = [], array $groups = []): ?string
    {
        $options[self::ONLY_CHART] = (isset($options[self::ONLY_CHART])) ? (bool) $options[self::ONLY_CHART] : false;
        $attributes = $options[self::DIV_ATTR] ?? [];
        array_walk($attributes, function (&$value, string $key): void {
            $value = sprintf('%s="%s"', $key, $value);
        });
        try {
            return $this->twig->render('@Chart/charts.twig', [
                self::ONLY_CHART => $options[self::ONLY_CHART],
                self::DIV_ATTR => implode(" ", $attributes),
                'statistics' => $this->chartDataCompiler->compileChart($options, $groups),
            ]);
        } catch (\Exception $ex) {

            return sprintf("'%s' line %s: %s", $ex->getFile(), $ex->getLine(), $ex->getMessage());
        }
    }
}

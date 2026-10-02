<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Compiler;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Kematjaya\ChartBundle\Builder\ChartBuilderInterface;
use Kematjaya\ChartBundle\Builder\ChartRendererBuilderInterface;
use Kematjaya\ChartBundle\Chart\AbstractChart;
use Kematjaya\ChartBundle\Chart\ClickableChartInterface;
use Kematjaya\ChartBundle\Chart\GroupChartInterface;
use Kematjaya\ChartBundle\Chart\SummaryTableRepositoryInterface;
use Kematjaya\ChartBundle\Event\ChartPointClickCreatedEvent;
use Kematjaya\ChartBundle\Event\PreBuildTableLinkEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Description of HighChartDataCompiler
 *
 * @author guest
 */
class HighChartDataCompiler implements ChartDataCompilerInterface
{
    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ChartRendererBuilderInterface $chartRendererBuilder,
        private readonly TokenStorageInterface $tokenStorage,
    ) {}

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

            $id = bin2hex(random_bytes(16));

            $data->offsetSet($id, $this->render($id, $chart, $options));
        }

        return $data;
    }

    protected function render(string $id, AbstractChart $chart, array $options): array
    {
        $chartRenderer = $this->chartRendererBuilder->getChartRenderer($chart);


        $qb = $chart->getQueryBuilder('t', $options['filter'] ?? []);
        $graph = json_encode(
            $chartRenderer->render($chart, $qb)
        );
        if (false === $graph) {
            throw new \Exception(sprintf("cannot encode chart '%s': %s", $chart::class, json_last_error_msg()));
        }

        $table = null;
        if ($chart instanceof SummaryTableRepositoryInterface) {
            $table = [
                'header' => $chart->getHeaders(),
                'data' => $this->buildTableData($chart, $qb),
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
            'table_active' => $graph ? '' : 'active',
            'width' => $chart->getWidth(),
            'clickable' => $clickableLink,
        ];
    }

    protected function isValidObject(mixed $chart, array $groups = []): bool
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

        $className = $chart::class;
        $objectGroups = call_user_func([$className, 'getGroups']);
        $selectedGroups = array_filter($objectGroups, fn($group): bool => in_array($group, $groups));

        return !empty($selectedGroups);
    }

    protected function buildTableData(SummaryTableRepositoryInterface $chart, QueryBuilder $queryBuilder): array
    {
        if (!$chart instanceof ClickableChartInterface) {

            return $chart->getDatas($queryBuilder);
        }

        return array_map(function (array $row) use ($chart, $queryBuilder): array {
            $modalAttribute = [];
            if ($chart->getModalDOMId()) {
                // bootstrap 4 (data-*) dan bootstrap 5 (data-bs-*)
                $modalAttribute[] = 'data-toggle="modal"';
                $modalAttribute[] = sprintf('data-target="%s"', $chart->getModalDOMId());
                $modalAttribute[] = 'data-bs-toggle="modal"';
                $modalAttribute[] = sprintf('data-bs-target="%s"', $chart->getModalDOMId());
            }

            $keys = array_keys($row);
            $label = $keys[0];
            $value = $keys[count($keys) - 1];
            $queryKey = $chart->getQueryKey() ?? 'q';

            $event = $this->dispatchPreBuildTableLink(
                new PreBuildTableLinkEvent($queryBuilder, $chart, (string) $row[$label])
            );
            $row[$value] = sprintf('<a href="%s?%s=%s" %s>%s</a>', $chart->getURL($queryBuilder), rawurlencode($queryKey), rawurlencode($event->getValue()), implode(" ", $modalAttribute), $row[$value]);

            return $row;
        }, $chart->getDatas($queryBuilder));
    }

    protected function buildClickPoint(ClickableChartInterface $chart, QueryBuilder $queryBuilder): string
    {
        $function = 'function (event) {
            let query = event.point.category;
            if (typeof query == "undefined") {
                query = event.point.name;
            }
            query = encodeURIComponent(query);
            %s
        }';

        $queryKey = $chart->getQueryKey() ?? 'q';
        if (!$chart->getModalDOMId()) {
            $actions = sprintf('window.location.href = "%s?%s=" + query;', $chart->getURL($queryBuilder), $queryKey);

            $event = $this->eventDispatcher->dispatch(
                new ChartPointClickCreatedEvent($chart, $queryBuilder, sprintf($function, $actions)),
                ChartPointClickCreatedEvent::EVENT_NAME
            );

            return $event->getValue();
        }

        $actions = sprintf(
            ''
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

    protected function getSingleRole(): ?string
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {

            return null;
        }

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {

            return null;
        }

        // mis. Kematjaya\UserBundle\Entity\DefaultUser
        if (method_exists($user, 'getSingleRole')) {

            return $user->getSingleRole();
        }

        $roles = $user->getRoles();
        if (empty($roles)) {

            return null;
        }

        return (string) end($roles);
    }

    /**
     * Dispatch with PreBuildTableLinkEvent::EVENT_NAME, listeners registered with the class name
     * (default dispatcher name) still receive the event
     */
    protected function dispatchPreBuildTableLink(PreBuildTableLinkEvent $event): PreBuildTableLinkEvent
    {
        $event = $this->eventDispatcher->dispatch($event, PreBuildTableLinkEvent::EVENT_NAME);
        if ($this->eventDispatcher->hasListeners(PreBuildTableLinkEvent::class)) {
            $event = $this->eventDispatcher->dispatch($event, PreBuildTableLinkEvent::class);
        }

        return $event;
    }

}

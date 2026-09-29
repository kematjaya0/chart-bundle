<?php

namespace Kematjaya\ChartBundle\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Kematjaya\ChartBundle\Compiler\HighChartDataCompiler;
use Kematjaya\ChartBundle\Compiler\ChartDataCompilerInterface;
use Kematjaya\ChartBundle\Event\PreBuildTableLinkEvent;
use Kematjaya\ChartBundle\Event\ChartPointClickCreatedEvent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class BundleTest extends KernelTestCase 
{
    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }

    protected function setUp(): void
    {
        static::getContainer()->set(EntityManagerInterface::class, $this->createMock(EntityManagerInterface::class));
    }

    private function login(UserInterface $user): void
    {
        static::getContainer()->get(TokenStorageInterface::class)->setToken(new UsernamePasswordToken($user, 'main', $user->getRoles()));
    }

    private function titles(iterable $charts): array
    {
        $titles = [];
        foreach ($charts as $chart) {
            $titles[] = $chart['title'];
        }

        return $titles;
    }

    public function testChartCompiler()
    {
        $container = static::getContainer();
        $compiler = $container->get(ChartDataCompilerInterface::class);
        $this->assertInstanceOf(HighChartDataCompiler::class, $compiler);

        // tanpa login: semua chart, yang punya sequence diurutkan lebih dulu
        $this->assertSame(['Pie test', 'Bar test', 'Admin line'], $this->titles($compiler->compileChart([], [])));
        // filter grup: chart tanpa grup tetap tampil
        $this->assertSame(['Pie test', 'Bar test', 'Admin line'], $this->titles($compiler->compileChart([], ["test"])));
        $this->assertSame(['Pie test', 'Admin line'], $this->titles($compiler->compileChart([], ["dummy"])));
    }

    public function testChartByRole()
    {
        $compiler = static::getContainer()->get(ChartDataCompilerInterface::class);

        $this->login(new InMemoryUser('user', null, ['ROLE_USER']));
        $this->assertSame(['Pie test', 'Bar test'], $this->titles($compiler->compileChart()));

        $this->login(new InMemoryUser('admin', null, ['ROLE_USER', 'ROLE_ADMIN']));
        $this->assertSame(['Pie test', 'Bar test', 'Admin line'], $this->titles($compiler->compileChart()));

        // user tanpa role
        $this->login(new InMemoryUser('none', null, []));
        $this->assertCount(3, $compiler->compileChart());
    }

    public function testSingleRoleUser()
    {
        $this->login(new SingleRoleUser('ROLE_ADMIN'));
        $charts = static::getContainer()->get(ChartDataCompilerInterface::class)->compileChart();

        $this->assertContains('Admin line', $this->titles($charts));
    }

    public function testUserBundleDefaultUser()
    {
        if (!class_exists(\Kematjaya\UserBundle\Entity\DefaultUser::class)) {
            $this->markTestSkipped('kematjaya/user-bundle tidak terpasang');
        }

        $user = (new \Kematjaya\UserBundle\Entity\DefaultUser())
            ->setUsername('admin')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $this->login($user);

        $this->assertContains('Admin line', $this->titles(static::getContainer()->get(ChartDataCompilerInterface::class)->compileChart()));
    }

    public function testTableAndClick()
    {
        $charts = array_values(static::getContainer()->get(ChartDataCompilerInterface::class)->compileChart()->toArray());
        $bar = $charts[1];

        $this->assertSame(['label', 'total'], $bar['table']['header']);
        $this->assertSame('<a href="foo/bar?q=Toko%20A%26B" data-toggle="modal" data-target="#test" data-bs-toggle="modal" data-bs-target="#test">1</a>', $bar['table']['data'][0]['total']);
        $this->assertStringStartsWith('<a href="foo/bar?q=" ', $bar['table']['data'][1]['total']);
        $this->assertStringContainsString('$("#test").modal("show")', $bar['clickable']['"%func%"']);
        $this->assertStringContainsString('encodeURIComponent(query)', $bar['clickable']['"%func%"']);

        $line = $charts[2];
        $this->assertStringContainsString('window.location.href = "/admin/detail?kategori=" + query;', $line['clickable']['"%func%"']);
        $this->assertSame(12.0, $line['width']);
        $this->assertSame(6.0, $charts[0]['width']);
    }

    public function testEvents()
    {
        $dispatcher = static::getContainer()->get('event_dispatcher');
        $dispatcher->addListener(PreBuildTableLinkEvent::EVENT_NAME, function (PreBuildTableLinkEvent $event) {
            $event->setValue('by-name-' . $event->getValue());
        });
        $dispatcher->addListener(PreBuildTableLinkEvent::class, function (PreBuildTableLinkEvent $event) {
            $event->setValue($event->getValue() . '-by-class');
        });
        $dispatcher->addListener(ChartPointClickCreatedEvent::EVENT_NAME, function (ChartPointClickCreatedEvent $event) {
            $event->setValue('function () {}');
        });

        $charts = array_values(static::getContainer()->get(ChartDataCompilerInterface::class)->compileChart()->toArray());

        $this->assertStringStartsWith('<a href="foo/bar?q=by-name-Toko%20A%26B-by-class" ', $charts[1]['table']['data'][0]['total']);
        $this->assertSame('function () {}', $charts[1]['clickable']['"%func%"']);
    }

    public function testRenderTwig()
    {
        $twig = static::getContainer()->get('twig');
        $html = $twig->createTemplate('{{ chart_javascript() }}{{ chart_stylesheet() }}{{ render_chart({"div_attr": {"style": "height:300px"}}, ["test"]) }}')->render();

        $this->assertStringContainsString('/bundles/chart/plugin/highcharts/highcharts.js', $html);
        $this->assertStringContainsString('.highcharts-data-table', $html);
        $this->assertSame(3, substr_count($html, 'Highcharts.chart('));
        $this->assertStringContainsString('style="height:300px"', $html);
        $this->assertStringContainsString('"click":function (event)', $html);
        $this->assertStringContainsString('"type":"pie"', $html);
        $this->assertStringNotContainsString('%func%', $html);
    }

    public function testRenderOnlyChart()
    {
        $html = static::getContainer()->get('twig')->createTemplate('{{ render_chart({"only_chart": true}, ["dummy"]) }}')->render();

        $this->assertStringNotContainsString('card-header', $html);
        $this->assertSame(2, substr_count($html, 'Highcharts.chart('));
    }
}

/**
 * User dengan getSingleRole() seperti Kematjaya\UserBundle\Entity\DefaultUser
 */
class SingleRoleUser implements UserInterface
{
    private $role;

    public function __construct(string $role)
    {
        $this->role = $role;
    }

    public function getSingleRole(): ?string
    {
        return $this->role;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER', $this->role, 'ROLE_OTHER'];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return 'single';
    }
}

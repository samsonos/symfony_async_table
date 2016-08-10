<?php

namespace Samsonos\AsyncTableBundle\Tests;

use PHPUnit\Framework\TestCase;
use Samsonos\AsyncTableBundle\Metadata\TableMetadata;
use Samsonos\AsyncTableBundle\Service\Renderer;
use Samsonos\AsyncTableBundle\Service\ViewConfig;
use Samsonos\AsyncTableBundle\Twig\AsyncTablePaginationExtension;

class RendererTest extends TestCase
{
    public function testRenderHeader()
    {
        $twig = static::createMock(\Twig_Environment::class);
        $twigTemplateMock = static::createMock(\Twig_Template::class);
        $twigTemplateMock->expects(static::once())->method('renderBlock')->willReturn('block-content');

        $twig->expects(static::once())->method('loadTemplate')->willReturn($twigTemplateMock);
        $twig->expects(static::once())->method('mergeGlobals')->willReturn([]);

        $paginationExt = static::createMock(AsyncTablePaginationExtension::class);

        $viewConfig = static::createMock(ViewConfig::class);
        $viewConfig->method('getViewPath')->willReturn('');

        $tableMetadata = static::createMock(TableMetadata::class);
        $renderer = new Renderer($twig, $paginationExt, $viewConfig);

        static::assertEquals('block-content', $renderer->renderHeader($tableMetadata));
    }

    public function textInstantiating()
    {
        $twig = static::createMock(\Twig_Environment::class);
        $paginationExt = static::createMock(AsyncTablePaginationExtension::class);
        $viewConfig = static::createMock(ViewConfig::class);

        new Renderer($twig, $paginationExt, $viewConfig);
    }

    public function testRenderPagination()
    {
        $twig = static::createMock(\Twig_Environment::class);
        $paginationExt = static::createMock(AsyncTablePaginationExtension::class);
        $paginationExt->expects(static::once())->method('render')->willreturn('pagination-block');

        $viewConfig = static::createMock(ViewConfig::class);

        $tableMetadata = static::createMock(TableMetadata::class);
        $renderer = new Renderer($twig, $paginationExt, $viewConfig);

        static::assertEquals('pagination-block', $renderer->renderPagination($tableMetadata));
    }

    public function testRenderBody()
    {
        $twig = static::createMock(\Twig_Environment::class);
        $paginationExt = static::createMock(AsyncTablePaginationExtension::class);
        $twig->expects(static::once())->method('render')->willReturn('body-block');

        $viewConfig = static::createMock(ViewConfig::class);

        $tableMetadata = static::createMock(TableMetadata::class);
        $tableMetadata->view = '';
        $tableMetadata->viewData = [];

        $renderer = new Renderer($twig, $paginationExt, $viewConfig);

        static::assertEquals('body-block', $renderer->renderBody($tableMetadata));
    }
}
<?php

namespace Samsonos\AsyncTable\Tests;

use PHPUnit\Framework\TestCase;
use Knp\Component\Pager\Paginator;
use Samsonos\AsyncTable\Metadata\ColumnMetadata;
use Samsonos\AsyncTable\Metadata\FilterMetadata;
use Samsonos\AsyncTable\Metadata\TableMetadata;
use Samsonos\AsyncTable\Service\AsyncTable;
use Samsonos\AsyncTable\Service\Renderer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\HeaderBag;

class AsyncTableTest extends TestCase
{
    public function getProperty($obj, $name)
    {
        $reflection = new \ReflectionClass($obj);
        $reflection_property = $reflection->getProperty($name);
        $reflection_property->setAccessible(true);
        return $reflection_property->getValue($obj);
    }


    public function testSetPaginationDataName()
    {
        $paginator = static::createMock(Paginator::class);
        $renderer = static::createMock(Renderer::class);

        $async = new AsyncTable($paginator, $renderer);
        $async->setPaginationDataName('name');

        static::assertEquals('name', $this->getProperty($async, 'paginationDataName'));
    }

    public function testUnderscore1()
    {
        $paginator = static::createMock(Paginator::class);
        $renderer = static::createMock(Renderer::class);

        $async = new AsyncTable($paginator, $renderer);
        static::assertEquals('order_id', $async->underscore('Order id'));
    }

    public function testUnderscore2()
    {
        $paginator = static::createMock(Paginator::class);
        $renderer = static::createMock(Renderer::class);

        $async = new AsyncTable($paginator, $renderer);
        static::assertEquals('order_id_', $async->underscore('Order id_'));
    }

    public function testCreateTableQueryNotFound()
    {
        $paginator = static::createMock(Paginator::class);
        $renderer = static::createMock(Renderer::class);

        static::expectException(\Exception::class);

        $async = new AsyncTable($paginator, $renderer);
        $async->createTable('view', []);
    }

    public function testCreateTableColumnsNotFound()
    {
        $paginator = static::createMock(Paginator::class);
        $renderer = static::createMock(Renderer::class);

        static::expectException(\Exception::class);

        $async = new AsyncTable($paginator, $renderer);
        $async->createTable('view', ['query' => '']);
    }

    public function testCreateTableEmptyColumns()
    {
        $paginator = static::createMock(Paginator::class);
        $paginator->expects(static::once())->method('paginate')->willReturn([]);
        $renderer = static::createMock(Renderer::class);

        $table = new TableMetadata();
        $table->view = 'view';
        $table->internalId = 'id';
        $table->pagination = [];
        $table->viewData = ['pagination' => []];

        $async = new AsyncTable($paginator, $renderer);
        $actual = $async->createTable('view', ['query' => '', 'columns' => []]);
        $actual->internalId = 'id';

        static::assertEquals($table, $actual);
    }

    public function testCreateTableSingleColumn()
    {
        $paginator = static::createMock(Paginator::class);
        $paginator->expects(static::once())->method('paginate')->willReturn([]);
        $renderer = static::createMock(Renderer::class);

        $table = new TableMetadata();
        $table->view = 'view';
        $table->internalId = 'id';
        $table->pagination = [];
        $table->viewData = ['pagination' => []];

        $column = new ColumnMetadata('Order id', 'p.order_id');
        $table->columns[] = $column;

        $async = new AsyncTable($paginator, $renderer);
        $actual = $async->createTable('view', ['query' => '', 'columns' => [
            ['title' => 'Order id', 'selector' => 'p.order_id']
        ]]);
        $actual->internalId = 'id';

        static::assertEquals($table, $actual);
    }

    public function testCreateTableSingleColumnWithEmptyFilter()
    {
        $paginator = static::createMock(Paginator::class);
        $paginator->expects(static::once())->method('paginate')->willReturn([]);
        $renderer = static::createMock(Renderer::class);

        $table = new TableMetadata();
        $table->view = 'view';
        $table->internalId = 'id';
        $table->pagination = [];
        $table->viewData = ['pagination' => []];

        $filter = new FilterMetadata('order_id', FilterMetadata::TYPE_INPUT, 'Order id');
        $column = new ColumnMetadata('Order id', 'p.order_id', $filter);
        $table->columns[] = $column;

        $async = new AsyncTable($paginator, $renderer);
        $actual = $async->createTable('view', ['query' => '', 'columns' => [
            ['title' => 'Order id', 'selector' => 'p.order_id', 'filter' => true]
        ]]);
        $actual->internalId = 'id';

        static::assertEquals($table, $actual);
    }

    public function testCreateTableSingleColumnWithCheckboxFilter()
    {
        $paginator = static::createMock(Paginator::class);
        $paginator->expects(static::once())->method('paginate')->willReturn([]);
        $renderer = static::createMock(Renderer::class);

        $table = new TableMetadata();
        $table->view = 'view';
        $table->internalId = 'id';
        $table->pagination = [];
        $table->viewData = ['pagination' => []];

        $filter = new FilterMetadata('order1_id', FilterMetadata::TYPE_CHECKBOX, 'Order id');
        $filter->defaultValue = '1';
        $column = new ColumnMetadata('Order id', 'p.order_id', $filter);
        $table->columns[] = $column;

        $async = new AsyncTable($paginator, $renderer);
        $actual = $async->createTable('view', ['query' => '', 'columns' => [
            ['title' => 'Order id', 'selector' => 'p.order_id', 'filter' => [
                'name' => 'order1_id',
                'type' => FilterMetadata::TYPE_CHECKBOX,
                'default_value' => '1'
            ]]
        ]]);
        $actual->internalId = 'id';

        static::assertEquals($table, $actual);
    }

    public function testCreateTableSingleColumnWithSelectFilter()
    {
        $paginator = static::createMock(Paginator::class);
        $paginator->expects(static::once())->method('paginate')->willReturn([]);
        $renderer = static::createMock(Renderer::class);

        $table = new TableMetadata();
        $table->view = 'view';
        $table->internalId = 'id';
        $table->pagination = [];
        $table->viewData = ['pagination' => []];

        $filter = new FilterMetadata('order1_id', FilterMetadata::TYPE_SELECT, 'Order id');
        $filter->defaultValue = 'hello';
        $filter->emptyPlaceholder = 'hello';
        $filter->options = ['Hello' => 'hello', 'hey' => 'hey'];
        $column = new ColumnMetadata('Order id', 'p.order_id', $filter);
        $table->columns[] = $column;

        $async = new AsyncTable($paginator, $renderer);
        $actual = $async->createTable('view', ['query' => '', 'columns' => [
            ['title' => 'Order id', 'selector' => 'p.order_id', 'filter' => [
                'name' => 'order1_id',
                'type' => FilterMetadata::TYPE_SELECT,
                'default_value' => 'hello',
                'options' => ['Hello' => 'hello', 'hey'],
                'empty_placeholder' => 'hello'
            ]]
        ]]);
        $actual->internalId = 'id';

        static::assertEquals($table, $actual);
    }

    public function testHandleContent()
    {
        $paginator = static::createMock(Paginator::class);

        $renderer = static::createMock(Renderer::class);
        $renderer->expects(static::once())->method('renderBody')->willReturn('');
        $renderer->expects(static::once())->method('renderPagination')->willReturn('');
        $renderer->expects(static::once())->method('renderHeader')->willReturn('');

        $request = static::createMock(Request::class);
        $request->expects(static::once())->method('isXmlHttpRequest')->willReturn(true);
        $headerBug = static::createMock(HeaderBag::class);
        $headerBug->expects(static::once())->method('get')->willReturn(true);
        $request->headers = $headerBug;

        $table = new TableMetadata();

        $async = new AsyncTable($paginator, $renderer);
        $actual = $async->handleContent($request, $table);

        static::assertInstanceOf(JsonResponse::class, $actual);
    }

    public function testHandleContentWithoutContent()
    {
        $paginator = static::createMock(Paginator::class);

        $renderer = static::createMock(Renderer::class);
        $renderer->expects(static::never())->method('renderBody')->willReturn('');
        $renderer->expects(static::never())->method('renderPagination')->willReturn('');
        $renderer->expects(static::never())->method('renderHeader')->willReturn('');

        $request = static::createMock(Request::class);
        $request->expects(static::once())->method('isXmlHttpRequest')->willReturn(false);
        $headerBug = static::createMock(HeaderBag::class);
        $headerBug->expects(static::never())->method('get')->willReturn(true);
        $request->headers = $headerBug;

        $table = new TableMetadata();

        $async = new AsyncTable($paginator, $renderer);
        $actual = $async->handleContent($request, $table);

        static::assertNull($actual);
    }
}
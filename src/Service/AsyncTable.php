<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 23.07.2015
 * Time: 15:34
 */

namespace Samsonos\AsyncTable\Service;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;
use Samsonos\AsyncTable\Metadata\ColumnMetadata;
use Samsonos\AsyncTable\Metadata\FilterMetadata;
use Samsonos\AsyncTable\Metadata\TableMetadata;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AsyncTable
 *
 * @package Samsonos\AsyncTable\Service
 */
class AsyncTable
{
    // Ajax request header
    const HEADER_NAME = 'x-samsonos-async-table-request';

    const DEFAULT_PAGE_COUNT = 10;
    const DEFAULT_CURRENT_PAGE = 1;

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @var string
     */
    protected $paginationDataName = 'pagination';

    /**
     * AsyncTable constructor
     *
     * @param Paginator $paginator
     * @param Renderer $renderer
     */
    public function __construct(Paginator $paginator, Renderer $renderer)
    {
        $this->renderer = $renderer;
        $this->paginator = $paginator;
    }

    /**
     * Set new pagination data name
     *
     * @param $name
     */
    public function setPaginationDataName($name)
    {
        $this->paginationDataName = $name;
    }

    /**
     * Get table metadata
     *
     * @param string $view
     * @param array $data
     * @return TableMetadata
     * @throws \Exception
     */
    public function createTable($view, $data)
    {
        // Check arbitrary params
        if (!array_key_exists('query', $data)) {
            throw new \InvalidArgumentException('Query not found in pagination data');
        }
        if (!array_key_exists('columns', $data)) {
            throw new \InvalidArgumentException('Columns not found');
        }

        // Create metadata
        $table = new TableMetadata();
        $pagination = $this->getPagination($data, $table);
        $table->pagination = $pagination;

        // Set main view
        $table->view = $view;
        $table->viewData = array_merge(isset($data['viewData']) ? $data['viewData'] : [], [$this->paginationDataName => $pagination]);

        // Iterate columns and set values
        foreach ($data['columns'] as $value) {
            $filter = null;
            $title = $value['title'] ?? null;
            // Set filter values
            if (array_key_exists('filter', $value)) {
                $filterValue = $value['filter'] === true ? [] : $value['filter'];
                $filter = new FilterMetadata(
                    $filterValue['name'] ?? $this->underscore($title),
                    $filterValue['type'] ?? null,
                    $filterValue['title'] ?? $title
                );
                // Default value of filter
                if (array_key_exists('default_value', $filterValue)) {
                    $filter->defaultValue = $filterValue['default_value'];
                }
                // Options for selector
                if (array_key_exists('options', $filterValue) && is_array($filterValue['options'])) {
                    $opts = [];
                    foreach ($filterValue['options'] as $key => $val) {
                        if (is_string($key)) {
                            $opts[$key] = $val;
                        } else {
                            $opts[$val] = $val;
                        }
                    }
                    $filter->options = $opts;
                }
                // Empty value for selector filter type
                if (array_key_exists('empty_placeholder', $filterValue)) {
                    $filter->emptyPlaceholder = $filterValue['empty_placeholder'];
                }
            }

            // Add new column
            $table->columns[] = new ColumnMetadata($title, $value['selector'] ?? null, $filter);
        }

        $this->data = $data;

        return $table;
    }

    /**
     * Get pagination by data
     *
     * @param $data
     * @param TableMetadata $tableMetadata
     * @param boolean $deep
     * @return PaginationInterface
     * @throws \LogicException
     */
    protected function getPagination($data, TableMetadata $tableMetadata, $deep = false)
    {
        $page = array_key_exists('page', $data) ? $data['page'] : self::DEFAULT_CURRENT_PAGE;
        $pageCount = array_key_exists('pageCount', $data) ? $data['pageCount'] : self::DEFAULT_PAGE_COUNT;

        // Create pagination
        $pagination = $this->paginator->paginate($data['query'], $page, $pageCount);


        // If count of items more than exists on the page then reload the pagination with correct page number
        if ($deep === false && ((($page - 1) * $pageCount) > $pagination->getTotalItemCount())) {
            $data['page'] = 1;
            $tableMetadata->isShowPagination = false;
            $pagination = $this->getPagination($data, $tableMetadata, true);
        }

        return $pagination;
    }

    /**
     * Get data for client
     *
     * @param Request $request
     * @param TableMetadata $tableMetadata
     * @return null|JsonResponse
     */
    public function handleContent(Request $request, TableMetadata $tableMetadata)
    {
        $isAsyncTableRequest = $request->isXmlHttpRequest() &&
            $request->headers->get(self::HEADER_NAME);

        // If there is async table ajax request
        if ($isAsyncTableRequest) {
            return new JsonResponse([
                'body' => $this->renderer->renderBody($tableMetadata),
                'pagination' => $this->renderer->renderPagination($tableMetadata),
                'header' => $this->renderer->renderHeader($tableMetadata),
                'isShowPagination' => $tableMetadata->isShowPagination
            ]);
        }
        // Do something
        return null;
    }

    /**
     * A string to underscore.
     *
     * @param string $string
     *
     * @return string The underscored string
     */
    public function underscore($string)
    {
        return strtolower(preg_replace('/[^A-Za-z_]/', '', preg_replace('/\s+/', '_', $string)));
    }
}

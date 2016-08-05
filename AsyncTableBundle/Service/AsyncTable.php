<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 23.07.2015
 * Time: 15:34
 */

namespace Samsonos\AsyncTableBundle\Service;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;
use Samsonos\AsyncTableBundle\Metadata\ColumnMetadata;
use Samsonos\AsyncTableBundle\Metadata\FilterMetadata;
use Samsonos\AsyncTableBundle\Metadata\TableMetadata;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AsyncTable
 *
 * @package Samsonos\AsyncTableBundle\Service
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
     * @param array $paginationData
     * @param array $columns
     * @param string $view
     * @param array $viewData
     * @return TableMetadata
     * @throws \Exception
     */
    public function createTable($view = null, array $paginationData = [], array $columns = [], array $viewData = [])
    {
        // Check arbitrary params
        if (!array_key_exists('query', $paginationData)) {
            throw new \Exception('Query not found in pagination data');
        }
        if (!$view) {
            throw new \Exception('View not found');
        }

        // Create pagination
        $pagination = $this->paginator->paginate(
            $paginationData['query'],
            array_key_exists('page', $paginationData) ? $paginationData['page'] : self::DEFAULT_CURRENT_PAGE,
            array_key_exists('pageCount', $paginationData) ? $paginationData['pageCount'] : self::DEFAULT_PAGE_COUNT
        );

        // Create metadata
        $table = new TableMetadata();
        $table->pagination = $pagination;

        // Set main view
        $table->view = $view;
        $viewData = array_merge($viewData, [$this->paginationDataName => $pagination]);
        $table->viewData = $viewData;

        // Iterate columns and set values
        foreach ($columns as $value) {
            $filter = null;
            $title = isset($value['title']) ? $value['title'] : null;
            // Set filter values
            if (isset($value['filter'])) {
                $filterValue = $value['filter'] === true ? [] : $value['filter'];
                $filter = new FilterMetadata(
                    isset($filterValue['name']) ? $filterValue['name'] : $this->underscore($title),
                    isset($filterValue['type']) ? $filterValue['type'] : null,
                    isset($filterValue['title']) ? $filterValue['title'] : $title
                );
                // Default value of filter
                if (isset($filterValue['default_value'])) {
                    $filter->defaultValue = $filterValue['default_value'];
                }
                // Options for selector
                if (isset($filterValue['options'])) {
                    $filter->options = $filterValue['options'];
                }
                // Empty value for selector filter type
                if (isset($filterValue['empty_placeholder'])) {
                    $filter->emptyPlaceholder = $filterValue['empty_placeholder'];
                }
            }

            // Add new column
            $table->columns[] = new ColumnMetadata(
                $title,
                isset($value['selector']) ? $value['selector'] : null,
                $filter
            );
        }

        return $table;
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
                'header' => $this->renderer->renderHeader($tableMetadata)
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

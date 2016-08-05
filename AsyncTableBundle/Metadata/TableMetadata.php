<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 04.08.2016
 * Time: 17:01
 */
namespace Samsonos\AsyncTableBundle\Metadata;

use Knp\Component\Pager\Pagination\PaginationInterface;

class TableMetadata
{
    /**
     * @var PaginationInterface
     */
    public $pagination;

    /**
     * @var FilterMetadata[]
     */
    public $columns = [];
    public $filters = [];
    public $showFilter = true;
    public $showPagination = true;
    public $view;
    public $viewData;
    public $internalId;

    /**
     * TableMetadata constructor.
     */
    public function __construct()
    {
        // Generate id
        $this->internalId = preg_replace('/\..*$/', '', uniqid('samsonos-async-table-id-', true));
    }

    /**
     * Get pagination
     *
     * @return PaginationInterface
     */
    public function getPagination()
    {
        return $this->pagination;
    }
}

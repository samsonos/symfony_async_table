<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 04.08.2016
 * Time: 17:01
 */
namespace Samsonos\AsyncTable\Metadata;

class ColumnMetadata
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $querySelector;

    /**
     * @var FilterMetadata
     */
    public $filter;

    /**
     * ColumnMetadata constructor
     *
     * @param $title
     * @param $querySelector
     */
    public function __construct($title = null, $querySelector = null, FilterMetadata $filter = null)
    {
        $this->title = $title;
        $this->querySelector = $querySelector;
        $this->filter = $filter;
    }
}

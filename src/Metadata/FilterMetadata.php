<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 04.08.2016
 * Time: 17:01
 */
namespace Samsonos\AsyncTableBundle\Metadata;

class FilterMetadata
{
    // Single input
    const TYPE_INPUT = 0;

    // Select with options
    const TYPE_SELECT = 1;

    // Checkbox true/false
    const TYPE_CHECKBOX = 2;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $type;

    /**
     * @var string
     */
    public $defaultValue;

    /**
     * @var string[]
     */
    public $options;

    /**
     * Placeholder for select
     *
     * @var string[]
     */
    public $emptyPlaceholder;

    /**
     * FilterMetadata constructor
     *
     * @param $name
     * @param $title
     * @param $type
     */
    public function __construct($name, $type = self::TYPE_INPUT, $title = null)
    {
        $this->name = $name;
        $this->title = $title;
        $this->type = $type;
    }
}

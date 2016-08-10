<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 23.07.2015
 * Time: 15:34
 */
namespace Samsonos\AsyncTableBundle\Twig;

use Samsonos\AsyncTableBundle\Metadata\TableMetadata;

class AsyncTableExtension extends TwigTable {

    /** @var string Name of twig extension */
    public static $extensionName = 'async_table';
}

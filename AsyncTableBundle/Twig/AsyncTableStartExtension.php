<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 23.07.2015
 * Time: 15:34
 */
namespace Samsonos\AsyncTableBundle\Twig;

class AsyncTableStartExtension extends TwigTable {

    /** @var string Name of twig extension */
    public static $extensionName = 'async_table_start';

    /** @var string Path to view */
    public static $viewPath = 'AsyncTableBundle::start_extension.html.twig';
}

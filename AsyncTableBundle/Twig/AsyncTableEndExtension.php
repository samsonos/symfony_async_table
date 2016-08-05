<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 23.07.2015
 * Time: 15:34
 */
namespace Samsonos\AsyncTableBundle\Twig;

class AsyncTableEndExtension extends TwigTable {

    /** @var string Name of twig extension */
    public static $extensionName = 'async_table_end';

    /** @var string Path to view */
    public static $viewPath = 'AsyncTableBundle::end_extension.html.twig';
}

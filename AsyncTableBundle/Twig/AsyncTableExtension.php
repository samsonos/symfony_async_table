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

    /** @var string Path to view */
    public static $viewPath = 'AsyncTableBundle::table.html.twig';

    /**
     * Render view
     *
     * @param \Twig_Environment $environment
     * @param TableMetadata $table
     * @param string $view
     * @param array $viewData
     * @return string
     * @throws \Exception
     */
    public function render(\Twig_Environment $environment, TableMetadata $table, $view = null, array $viewData = [])
    {
        if ($view) {
            $table->view = $view;
        }
        if (count($viewData)) {
            $table->viewData = $viewData;
        }
        return $environment->render(static::$viewPath, ['table' => $table]);
    }
}

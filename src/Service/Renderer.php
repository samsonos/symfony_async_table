<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 04.08.2016
 * Time: 16:31
 */
namespace Samsonos\AsyncTable\Service;

use Samsonos\AsyncTable\Metadata\TableMetadata;
use Samsonos\AsyncTable\Twig\AsyncTableHeaderExtension;
use Samsonos\AsyncTable\Twig\AsyncTablePaginationExtension;

/**
 * Class Renderer
 *
 * @package Samsonos\AsyncTable\Service
 */
class Renderer
{
    /**
     * @var \Twig_Environment
     */
    public $twig;

    /**
     * @var AsyncTablePaginationExtension
     */
    public $paginationExtension;

    /**
     * @var ViewConfig
     */
    protected $viewConfig;

    /**
     * Renderer constructor
     *
     * @param \Twig_Environment $twig
     * @param AsyncTablePaginationExtension $paginationExtension
     */
    public function __construct(\Twig_Environment $twig, AsyncTablePaginationExtension $paginationExtension, ViewConfig $viewConfig)
    {
        $this->twig = $twig;
        $this->paginationExtension = $paginationExtension;
        $this->viewConfig = $viewConfig;
    }

    /**
     * Render header
     *
     * @param TableMetadata $tableMetadata
     * @return string
     * @throws \Exception
     */
    public function renderHeader(TableMetadata $tableMetadata)
    {
        /** @var \Twig_Template $template */
        $template = $this->twig->loadTemplate($this->viewConfig->getViewPath());
        return $template->renderBlock(
            AsyncTableHeaderExtension::$extensionName,
            $this->twig->mergeGlobals(['table' => $tableMetadata])
        );
    }

    /**
     * Render body
     *
     * @param TableMetadata $tableMetadata
     * @return string
     */
    public function renderBody(TableMetadata $tableMetadata)
    {
        return $this->twig->render($tableMetadata->view, $tableMetadata->viewData);
    }

    /**
     * Render pagination
     *
     * @param TableMetadata $tableMetadata
     * @return string
     */
    public function renderPagination(TableMetadata $tableMetadata)
    {
        return $this->paginationExtension->render($this->twig, $tableMetadata);
    }
}

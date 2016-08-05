<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 04.08.2016
 * Time: 18:19
 */
namespace Samsonos\AsyncTableBundle\Twig;

use Samsonos\AsyncTableBundle\Metadata\TableMetadata;
use Samsonos\AsyncTableBundle\Service\ViewConfig;

abstract class TwigTable extends \Twig_Extension
{
    /** @var string Name of twig extension */
    public static $extensionName;

    /** @var string Path to view */
    public static $viewPath;

    /**
     * @var ViewConfig
     */
    protected $viewConfig;

    /**
     * TwigTable constructor
     *
     * @param $viewConfig
     */
    public function __construct($viewConfig)
    {
        $this->viewConfig = $viewConfig;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return static::$extensionName . '_extension';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                static::$extensionName, [
                $this, 'render'
            ], [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
        ];
    }

    /**
     * Render view
     *
     * @param \Twig_Environment $environment
     * @param TableMetadata $table
     * @return string
     * @throws \Exception
     */
    public function render(\Twig_Environment $environment, TableMetadata $table)
    {
        return $environment->render($this->viewConfig->get(static::$extensionName), ['table' => $table]);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 23.07.2015
 * Time: 15:34
 */
namespace Samsonos\AsyncTable\Twig;

use Samsonos\AsyncTable\Service\ViewConfig;

class AsyncTableThemeExtension extends \Twig_Extension
{
    /** @var string Name of twig extension */
    public static $extensionName = 'async_table_theme';

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
                $this, 'setup'
            ], [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
        ];
    }

    /**
     * Set new path to view
     *
     * @param \Twig_Environment $environment
     * @param string $view
     * @return null
     */
    public function setup(\Twig_Environment $environment, $view)
    {
        $this->viewConfig->setViewPath($view);
    }
}

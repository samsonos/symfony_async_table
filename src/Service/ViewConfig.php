<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 05.08.2016
 * Time: 16:37
 */
namespace Samsonos\AsyncTableBundle\Service;

class ViewConfig
{
    /**
     * @var string
     */
    protected $view;

    /**
     * ViewConfig constructor
     *
     * @param string $view
     */
    public function __construct($view)
    {
        $this->view = $view;
    }

    /**
     * Get view path by name
     *
     * @return mixed
     * @throws \Exception
     */
    public function getViewPath()
    {
        return $this->view;
    }

    /**
     * Set view path by name
     *
     * @param $view
     * @throws \Exception
     */
    public function setViewPath($view)
    {
        if (!is_string($view)) {
            throw new \InvalidArgumentException('View is not a string');
        }
        $this->view = $view;
    }
}

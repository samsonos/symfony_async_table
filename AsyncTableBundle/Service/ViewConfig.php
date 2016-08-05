<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 05.08.2016
 * Time: 16:37
 */
namespace Samsonos\AsyncTableBundle\Service;

use Samsonos\AsyncTableBundle\DependencyInjection\AsyncTableExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ViewConfig
{
    /**
     * @var array
     */
    protected $list = [];

    /**
     * ViewConfig constructor
     *
     * @param array $list
     */
    public function __construct(array $list)
    {
        $this->list = $list;
    }

    /**
     * Get view path by name
     *
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function get($name)
    {
        if (!$this->list[$name]) {
            throw new \Exception(sprintf('View %s not found', $name));
        }
        return $this->list[$name];
    }

    /**
     * Set view path by name
     *
     * @param $name
     * @param $value
     * @throws \Exception
     */
    public function set($name, $value)
    {
        $this->list[$name] = $value;
    }
}

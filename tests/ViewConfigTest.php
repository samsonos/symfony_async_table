<?php

namespace Samsonos\AsyncTable\Tests;

use PHPUnit\Framework\TestCase;
use Samsonos\AsyncTable\Service\ViewConfig;

class ViewConfigTestTest extends TestCase
{
    public function testSetup()
    {
        $viewPath = 'view.html.twig';
        $viewConfig = new ViewConfig($viewPath);

        static::assertEquals($viewPath, $viewConfig->getViewPath());

        return $viewConfig;
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testWrongInstantiating()
    {
        new ViewConfig();
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     * @depends testSetup
     */
    public function testWrongSetupInstantiating(ViewConfig $viewConfig)
    {
        $viewConfig->setViewPath();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @depends testSetup
     */
    public function testIncorrectViewPath(ViewConfig $viewConfig)
    {
        $viewConfig->setViewPath(4);
    }

    /**
     * @depends testSetup
     */
    public function testEmptySet(ViewConfig $viewConfig)
    {
        static::assertEmpty($viewConfig->setViewPath('some.html.twig'));
    }
}
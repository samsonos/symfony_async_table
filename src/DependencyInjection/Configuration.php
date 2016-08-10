<?php

namespace Samsonos\AsyncTable\DependencyInjection;

use Samsonos\AsyncTable\Twig\AsyncTableEndExtension;
use Samsonos\AsyncTable\Twig\AsyncTableEndHeaderExtension;
use Samsonos\AsyncTable\Twig\AsyncTableEndTableExtension;
use Samsonos\AsyncTable\Twig\AsyncTableExtension as AsyncTableExtensionTwig;
use Samsonos\AsyncTable\Twig\AsyncTableFilterExtension;
use Samsonos\AsyncTable\Twig\AsyncTableHeaderExtension;
use Samsonos\AsyncTable\Twig\AsyncTableInitExtension;
use Samsonos\AsyncTable\Twig\AsyncTablePaginationExtension;
use Samsonos\AsyncTable\Twig\AsyncTableStartExtension;
use Samsonos\AsyncTable\Twig\AsyncTableStartHeaderExtension;
use Samsonos\AsyncTable\Twig\TwigTable;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('async_table');

        $rootNode
            ->children()
                // Set path to views
                ->scalarNode('view')->defaultValue(TwigTable::$defaultViewPath)->end()
            ->end();

        return $treeBuilder;
    }
}

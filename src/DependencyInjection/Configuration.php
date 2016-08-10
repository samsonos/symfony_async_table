<?php

namespace Samsonos\AsyncTableBundle\DependencyInjection;

use Samsonos\AsyncTableBundle\Twig\AsyncTableEndExtension;
use Samsonos\AsyncTableBundle\Twig\AsyncTableEndHeaderExtension;
use Samsonos\AsyncTableBundle\Twig\AsyncTableEndTableExtension;
use Samsonos\AsyncTableBundle\Twig\AsyncTableExtension as AsyncTableExtensionTwig;
use Samsonos\AsyncTableBundle\Twig\AsyncTableFilterExtension;
use Samsonos\AsyncTableBundle\Twig\AsyncTableHeaderExtension;
use Samsonos\AsyncTableBundle\Twig\AsyncTableInitExtension;
use Samsonos\AsyncTableBundle\Twig\AsyncTablePaginationExtension;
use Samsonos\AsyncTableBundle\Twig\AsyncTableStartExtension;
use Samsonos\AsyncTableBundle\Twig\AsyncTableStartHeaderExtension;
use Samsonos\AsyncTableBundle\Twig\TwigTable;
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

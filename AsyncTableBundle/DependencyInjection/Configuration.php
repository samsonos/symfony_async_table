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
                ->arrayNode('views')
                    ->isRequired()
                    ->children()
                        ->scalarNode(AsyncTableEndExtension::$extensionName)->defaultValue(AsyncTableEndExtension::$viewPath)->end()
                        ->scalarNode(AsyncTableEndHeaderExtension::$extensionName)->defaultValue(AsyncTableEndHeaderExtension::$viewPath)->end()
                        ->scalarNode(AsyncTableEndTableExtension::$extensionName)->defaultValue(AsyncTableEndTableExtension::$viewPath)->end()
                        ->scalarNode(AsyncTableExtensionTwig::$extensionName)->defaultValue(AsyncTableExtensionTwig::$viewPath)->end()
                        ->scalarNode(AsyncTableFilterExtension::$extensionName)->defaultValue(AsyncTableFilterExtension::$viewPath)->end()
                        ->scalarNode(AsyncTableHeaderExtension::$extensionName)->defaultValue(AsyncTableHeaderExtension::$viewPath)->end()
                        ->scalarNode(AsyncTableInitExtension::$extensionName)->defaultValue(AsyncTableInitExtension::$viewPath)->end()
                        ->scalarNode(AsyncTablePaginationExtension::$extensionName)->defaultValue(AsyncTablePaginationExtension::$viewPath)->end()
                        ->scalarNode(AsyncTableStartExtension::$extensionName)->defaultValue(AsyncTableStartExtension::$viewPath)->end()
                        ->scalarNode(AsyncTableStartHeaderExtension::$extensionName)->defaultValue(AsyncTableStartHeaderExtension::$viewPath)->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}

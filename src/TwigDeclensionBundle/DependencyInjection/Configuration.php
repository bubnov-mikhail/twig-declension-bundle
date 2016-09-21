<?php

namespace Bubnov\TwigDeclensionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bubnov.twig.declension');

        $rootNode
            ->children()
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('load_all')->defaultValue(true)->end()
                    ->scalarNode('auto-define')->defaultValue(true)->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

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
        $rootNode = $treeBuilder->root('bubnov_twig_declension');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('pre_cache')->defaultValue(true)->end()
                ->scalarNode('auto_create')->defaultValue(true)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

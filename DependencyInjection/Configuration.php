<?php

namespace MandarinMedien\MMCmfNodeBundle\DependencyInjection;

use MandarinMedien\MMCmfNodeBundle\Entity\Node;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mm_cmf_node');

        $rootNode
            ->children()
                ->scalarNode('defaultIcon')->defaultValue('fa-file-o')->end()
                ->scalarNode('class')->defaultValue(Node::class)->end()
                ->arrayNode('nodes')
                     ->prototype('array')
                        ->children()
                            ->scalarNode('icon')->defaultValue('fa-file-o')->end()
                            ->arrayNode('children')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}

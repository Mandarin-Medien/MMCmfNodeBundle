<?php

namespace MandarinMedien\MMCmfNodeBundle\DependencyInjection;

use MandarinMedien\MMCmfNodeBundle\Entity\Node;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
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
                ->variableNode('nodes')
                    ->defaultValue(array())
                    ->validate()->ifTrue(function($element) { return !is_array($element); })->thenInvalid('The children element must be an array.')->end()
                    ->validate()->always(function(array $nodes) { array_walk($nodes, [$this, "evaluateChild"]); return $nodes;})->end()
                ->end()
                ->arrayNode('routing')
                    ->children()
                        ->arrayNode('defaults')
                            ->children()
                                ->scalarNode('template')->end()
                            ->end()
                        ->end()
                        ->arrayNode('route_loader')
                            ->children()
                                ->arrayNode('_controllers')
                                    ->children()
                                        ->scalarNode('default')->end()
                                        ->scalarNode('auto')->end()
                                        ->scalarNode('alias')->end()
                                        ->scalarNode('redirect')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }


    /**
     * @param NodeDefinition $node
     * @return mixed
     */
    protected function buildChildNode(ArrayNodeDefinition $node)
    {

        return $node
            ->addDefaultsIfNotSet(true)
            ->children()
                ->scalarNode('icon')->defaultValue('fa-file-o')->end()
                ->arrayNode('templates')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('template')->end()
                        ->end()
                    ->end()
                ->end()
                ->variableNode('children')
                    ->defaultValue(array())
                    ->validate()
                        ->ifTrue(function($element) { return !is_array($element); })
                        ->thenInvalid('The children element must be an array.')
                    ->end()
                    ->validate()
                        ->always(function(array $nodes) {

                            // just validate the child without manipulation
                            foreach ($nodes as $name => $node)
                                $this->evaluateChild($node, $name);

                            return $nodes;
                        })
                    ->end()
                ->end()
            ->end();

    }


    protected function evaluateChild(&$node, $name)
    {
        $data = is_null($node)
            ? []
            : $node;

        $node = $this->getChildNode($name);

        $node->normalize($data);
        $node = $node->finalize($data);
    }


    /**
     * @param string $name
     * @return \Symfony\Component\Config\Definition\NodeInterface
     */
    protected function getChildNode($name = '')
    {
        $treeBuilder = new TreeBuilder();
        $definition = $treeBuilder->root($name);

        $this->buildChildNode($definition);

        return $definition->getNode(true);
    }
}

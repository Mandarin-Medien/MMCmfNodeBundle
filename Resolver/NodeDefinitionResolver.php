<?php

namespace MandarinMedien\MMCmfNodeBundle\Resolver;

use MandarinMedien\MMCmfNodeBundle\Configuration\NodeDefinition;
use MandarinMedien\MMCmfNodeBundle\Configuration\NodeRegistry;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;


/**
 * Class NodeDefinitionResolver
 *
 * The NodeDefinitionResolver resolves the node class configuration (icon, templates, allowed children etc)
 * from any given node in a hierarchy context
 *
 * @package MandarinMedien\MMCmfNodeBundle\Resolver
 */
class NodeDefinitionResolver
{

    const KEY_SEPARATOR = '__';


    /**
     * @var NodeRegistry
     */
    protected $registry;


    /**
     * NodeDefinitionResolver constructor.
     * @param NodeRegistry $registry
     */
    public function __construct(NodeRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * resolve Node Class Configuration from given Node
     * @param NodeInterface $node
     * @return NodeDefinition|null
     */
    public function resolve(NodeInterface $node): ?NodeDefinition
    {

        $definition = null;

        while ($node) {

            // build the search key
            // in each iteration the class of its parent is
            // prepended
            $search = isset($search)
                ? get_class($node) . self::KEY_SEPARATOR . $search
                : get_class($node);

            $definition = $this->registry->has($search)
                ? $this->registry->get($search)
                : $definition;

            $node = $node->getParent();
        }

        return $definition;
    }


    /**
     * resolve definition by key containing the class hierarchy
     * @param $key
     * @return NodeDefinition|null
     */
    public function resolveByKey($key): ?NodeDefinition
    {
        $definition = null;

        $pieces = array_reverse(explode(self::KEY_SEPARATOR, $key));

        while($piece = current($pieces))
        {
            $search = isset($search)
                ? $piece . self::KEY_SEPARATOR . $search
                : $piece;

            $definition = $this->registry->has($search)
                ? $this->registry->get($search)
                : $definition;

            next($pieces);
        }

        return $definition;
    }


    /**
     * @return NodeRegistry
     */
    public function getRegistry(): NodeRegistry
    {
        return $this->registry;
    }
}
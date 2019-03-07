<?php

namespace MandarinMedien\MMCmfNodeBundle\Resolver;

use MandarinMedien\MMCmfNodeBundle\Configuration\TagRegistry;
use MandarinMedien\MMCmfNodeBundle\Configuration\TemplateDefinition;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;

/**
 * Class TemplateDefinitionResolver
 *
 * The NodeTemplateResolver resolves available Templates by node context definitions
 *
 * @package MandarinMedien\MMCmfNodeBundle\Resolver
 */
class TemplateDefinitionResolver
{

    /**
     * @var NodeDefinitionResolver
     */
    protected $nodeDefinitionResolver;


    /**
     * @var TagRegistry
     */
    protected $tagRegistry;


    /**
     * TemplateDefinitionResolver constructor.
     * @param NodeDefinitionResolver $resolver
     * @param TagRegistry $tagRegistry
     */
    public function __construct(NodeDefinitionResolver $resolver, TagRegistry $tagRegistry)
    {
        $this->nodeDefinitionResolver = $resolver;
        $this->tagRegistry = $tagRegistry;
    }


    /**
     * @param NodeInterface $node
     * @return TemplateDefinition[]
     */
    public function resolve(NodeInterface $node)
    {
        if($definition = $this->nodeDefinitionResolver->resolve($node))
            return array_filter($definition->getTemplates(), function (TemplateDefinition $template) use ($node) {
                return is_null($template->getTags())
                    || array_intersect($template->getTags(), $this->getTags($node));
            });

        return [];
    }


    /**
     * traverse uo the parent nodes and collect its ids
     * @param NodeInterface $node
     * @return array
     */
    protected function getTags(NodeInterface $node)
    {
        $tags = [];

        while ($node) {
           if($this->tagRegistry->has($node->getId()))
               $tags[] = $this->tagRegistry->get($node->getId());

           $node = $node->getParent();
        }

        return $tags;
    }

}
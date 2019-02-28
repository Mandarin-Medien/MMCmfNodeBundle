<?php

namespace MandarinMedien\MMCmfNodeBundle\Resolver;

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
     * TemplateDefinitionResolver constructor.
     * @param NodeDefinitionResolver $resolver
     */
    public function __construct(NodeDefinitionResolver $resolver)
    {
        $this->nodeDefinitionResolver = $resolver;
    }


    /**
     * @param NodeInterface $node
     * @return TemplateDefinition[]
     */
    public function resolve(NodeInterface $node)
    {
        if($definition = $this->nodeDefinitionResolver->resolve($node))
            return array_filter($definition->getTemplates(), function (TemplateDefinition $template) use ($node) {
                return is_null($template->getNodes())
                    || array_intersect($template->getNodes(), $this->getIdList($node));
            });

        return [];
    }


    /**
     * traverse uo the parent nodes and collect its ids
     * @param NodeInterface $node
     * @return array
     */
    protected function getIdList(NodeInterface $node)
    {
        $ids = [$node->getId()];

        while ($node = $node->getParent())
            $ids[] = $node->getId();

        return $ids;
    }

}
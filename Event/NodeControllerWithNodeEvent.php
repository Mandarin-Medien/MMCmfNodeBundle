<?php
/**
 * Created by PhpStorm.
 * User: tonigurski
 * Date: 2019-03-07
 * Time: 16:37
 */

namespace MandarinMedien\MMCmfNodeBundle\Event;

use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRouteInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NodeControllerWithNodeEvent extends NodeControllerEvent
{
    /**
     * @var NodeInterface
     */
    private $node;

    /**
     * NodeControllerWithResponseEvent constructor.
     *
     * @param Request $request
     * @param NodeRouteInterface $nodeRoute
     * @param NodeInterface $node
     */
    public function __construct(Request $request, NodeRouteInterface $nodeRoute, NodeInterface $node)
    {
       parent::__construct($request,$nodeRoute);
        $this->node = $node;
    }

    /**
     * @return NodeInterface
     */
    public function getNode(): NodeInterface
    {
        return $this->node;
    }
}
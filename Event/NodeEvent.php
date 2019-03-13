<?php
/**
 * Created by PhpStorm.
 * User: tonigurski
 * Date: 2019-03-07
 * Time: 16:37
 */

namespace MandarinMedien\MMCmfNodeBundle\Event;

use MandarinMedien\MMCmfNodeBundle\Entity\NodeRouteInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class NodeEvent extends Event
{
    protected $request;
    protected $nodeRoute;

    /**
     * NodeEvent constructor.
     *
     * @param Request $request
     * @param NodeRouteInterface $nodeRoute
     */
    public function __construct(Request &$request, NodeRouteInterface &$nodeRoute)
    {
        $this->request = $request;
        $this->nodeRoute = $nodeRoute;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return NodeRouteInterface
     */
    public function getNodeRoute(): NodeRouteInterface
    {
        return $this->nodeRoute;
    }
}
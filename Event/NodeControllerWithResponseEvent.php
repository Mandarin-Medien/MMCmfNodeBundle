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
use Symfony\Component\HttpFoundation\Response;

class NodeControllerWithResponseEvent extends NodeControllerEvent
{
    /**
     * @var Response
     */
    private $response;

    /**
     * NodeControllerWithResponseEvent constructor.
     *
     * @param Request $request
     * @param NodeRouteInterface $nodeRoute
     * @param Response $response
     */
    public function __construct(Request $request, NodeRouteInterface $nodeRoute, Response $response)
    {
        parent::__construct($request, $nodeRoute);
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
}
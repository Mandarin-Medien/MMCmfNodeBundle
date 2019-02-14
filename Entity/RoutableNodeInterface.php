<?php

namespace MandarinMedien\MMCmfNodeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;


interface RoutableNodeInterface extends NodeInterface
{

    public function addRoute(NodeRouteInterface $nodeRoute);

    /**
     * @return ArrayCollection|NodeRoute[]
     */
    public function getRoutes();

    public function removeRoute(NodeRouteInterface $nodeRoute);

    public function hasAutoNodeRouteGeneration();

    public function setAutoNodeRouteGeneration($autoNodeRouteGeneration);


}
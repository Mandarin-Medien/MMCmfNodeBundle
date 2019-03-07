<?php

namespace MandarinMedien\MMCmfNodeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


interface RoutableNodeInterface extends NodeInterface
{
    /**
     * @param NodeRouteInterface $nodeRoute
     * @return self
     */
    public function addRoute(NodeRouteInterface $nodeRoute);

    /**
     * @return array|NodeRoute[]
     */
    public function getRoutes();

    /**
     * @param NodeRouteInterface $nodeRoute
     * @return self
     */
    public function removeRoute(NodeRouteInterface $nodeRoute);

    /**
     * @return boolean
     */
    public function hasAutoNodeRouteGeneration();

    /**
     * @param boolean $autoNodeRouteGeneration
     * @return self
     */
    public function setAutoNodeRouteGeneration($autoNodeRouteGeneration);


}
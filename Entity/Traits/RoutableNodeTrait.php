<?php

namespace MandarinMedien\MMCmfNodeBundle\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRouteInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\RoutableNodeInterface;

trait RoutableNodeTrait
{
    /**
     * @ORM\ManyToMany(targetEntity=NodeRoute::class, cascade={"all"})
     * @ORM\JoinTable(joinColumns={
     *     @ORM\JoinColumn(name="node_id", referencedColumnName="id", onDelete="cascade")
     * }, inverseJoinColumns={
     *     @ORM\JoinColumn(name="route_id", referencedColumnName="id", unique=true, onDelete="cascade")
     * })
     * @var ArrayCollection
     */
    protected $routes;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $autoNodeRouteGeneration = true;

    /**
     * @param NodeRouteInterface $nodeRoute
     * @return $this|RoutableNodeInterface
     */
    public function addRoute(NodeRouteInterface $nodeRoute)
    {
        $this->routes->add($nodeRoute);
        return $this;
    }

    /**
     * @param NodeRouteInterface $nodeRoute
     * @return $this|RoutableNodeInterface
     */
    public function removeRoute(NodeRouteInterface $nodeRoute)
    {
        $this->routes->removeElement($nodeRoute);
        return $this;
    }

    /**
     * @return ArrayCollection|NodeRoute[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @return bool
     */
    public function hasAutoNodeRouteGeneration()
    {
        return $this->autoNodeRouteGeneration;
    }

    /**
     * @param bool $autoNodeRouteGeneration
     * @return $this|RoutableNodeInterface
     */
    public function setAutoNodeRouteGeneration($autoNodeRouteGeneration)
    {
        $this->autoNodeRouteGeneration = $autoNodeRouteGeneration;
        return $this;
    }

}
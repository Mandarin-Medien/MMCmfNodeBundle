<?php

namespace MandarinMedien\MMCmfNodeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use MandarinMedien\MMCmfRoutingBundle\Entity\NodeRoute;

/**
 * Node
 */
class Node implements NodeInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;


    /**
     * @var NodeInterface
     */
    protected $parent;


    /**
     * @var ArrayCollection
     */
    protected $nodes;


    /**
     * @var NodeRoute[]
     */
    protected $routes;



    public function __construct()
    {
        $this->nodes = new ArrayCollection();
        $this->routes = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Node
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }


    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @return NodeInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param NodeInterface $parent
     */
    public function setParent(NodeInterface $node)
    {
        $this->parent = $node;
    }

    /**
     * @return NodeInterface[]
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * @param ArrayCollection $nodes
     * @return Node
     */
    public function setNodes(ArrayCollection $nodes)
    {
        $this->nodes = $nodes;
        return $this;
    }


    /**
     * @param NodeInterface $node
     * @return $this
     */
    public function addNode(NodeInterface $node)
    {
        $this->nodes->add($node);
        return $this;
    }


    /**
     * @param NodeInterface $node
     * @return $this
     */
    public function removeNode(NodeInterface $node)
    {
        $this->nodes->removeElement($node);
        return $this;
    }


    /**
     * @return NodeRoute
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param NodeRoute[] $routes
     * @return Node
     */
    public function setRoute($routes)
    {
        $this->routes = $routes;
        return $this;
    }

    /**
     * @param NodeRoute $route
     * @return $this
     */
    public function addRoute(NodeRoute $route)
    {
        $this->routes->add($route);
        $route->setNode($this);
        return $this;
    }


    /**
     * @param NodeRoute $route
     * @return $this
     */
    public function removeRoute(NodeRoute $route)
    {
        $this->routes->removeElement($route);
        return $this;
    }
}

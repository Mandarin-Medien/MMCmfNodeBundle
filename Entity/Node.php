<?php

namespace MandarinMedien\MMCmfNodeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use MandarinMedien\MMCmfRoutingBundle\Entity\NodeRoute;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank
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
     *
     * @Assert\Valid
     */
    protected $routes;


    /**
     * @var int
     */
    protected $position = 0;


    /**
     * @var boolean
     */
    protected $routeGeneration = false;


    /**
     * Node constructor.
     */
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
     * @param NodeInterface|null $node
     * @return $this
     */
    public function setParent(NodeInterface $node = null)
    {
        if ($node)
            $this->parent = $node;

        return $this;
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

        foreach ($this->nodes as $node)
            $node->setParent($this);

        return $this;
    }


    /**
     * @param NodeInterface $node
     * @return $this
     */
    public function addNode(NodeInterface $node)
    {
        $this->nodes->add($node);
        $node->setParent($this);

        return $this;
    }


    /**
     * @param NodeInterface $node
     * @return $this
     */
    public function removeNode(NodeInterface $node)
    {

        $this->nodes->removeElement($node);
        $node->setParent(null);

        return $this;
    }


    /**
     * @return NodeRoute[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param ArrayCollection $routes
     * @return Node
     */
    public function setRoutes(ArrayCollection $routes)
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
        $route->setNode(null);
        return $this;
    }


    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return boolean
     */
    public function isRouteGeneration()
    {
        return $this->routeGeneration;
    }

    /**
     * @param boolean $routeGeneration
     * @return Node
     */
    public function setRouteGeneration($routeGeneration)
    {
        $this->routeGeneration = $routeGeneration;
        return $this;
    }


    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }


    /**
     * @param int $position
     * @return Node
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }
}

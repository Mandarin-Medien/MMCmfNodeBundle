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
     * @var NodeRoute
     */
    protected $route;

    /**
     * Node constructor.
     */
    function __construct()
    {
        $this->nodes = new ArrayCollection();
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
     * @param NodeInterface $node
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

        foreach($this->nodes as $node)
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
     * @return NodeRoute
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param NodeRoute $route
     * @return Node
     */
    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }
}

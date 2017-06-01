<?php

namespace MandarinMedien\MMCmfNodeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use MandarinMedien\MMCmfRoutingBundle\Entity\NodeRoute;
use Symfony\Component\Validator\Constraints as Assert;
use MandarinMedien\MMCmfNodeBundle\Validator\Constraint as NodeAssert;

/**
 * Node
 * @NodeAssert\NoNodeRecursion
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
     * @var int
     */
    protected $position = 0;


    /**
     * @var bool
     */
    protected $visible = false;



    /**
     * Node constructor.
     */
    public function __construct()
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
     * @param NodeInterface|null $node
     * @return $this
     */
    public function setParent(NodeInterface $node = null)
    {
        $this->parent = $node;
        return $this;
    }

    /**
     * @return NodeInterface[]|ArrayCollection
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * @param ArrayCollection|NodeInterface[] $nodes
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

    public function __toString()
    {
        return (string)$this->getName();
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


    /**
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param boolean $visible
     * @return Node
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }

}

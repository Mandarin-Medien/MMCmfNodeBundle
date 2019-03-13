<?php

namespace MandarinMedien\MMCmfNodeBundle\Factory;


use MandarinMedien\MMCmfNodeBundle\Configuration\NodeDefinition;

class NodeMeta
{


    /**
     * @var string
     */
    public $name;


    /**
     * @var integer
     */
    public $id;


    /**
     * @var integer
     */
    public $position;


    /**
     * @var string
     */
    public $key;


    /**
     * @var NodeMeta
     */
    protected $parent;


    /**
     * @var NodeMeta[]
     */
    public $children;


    /**
     * @var NodeDefinition
     */
    public $definition;

    /**
     * @var array
     */
    public $tags;


    /**
     * @var string
     */
    public $classname;


    /**
     * @var string
     */
    public $dtype;


    public function __construct()
    {
        $this->children = [];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return NodeMeta
     */
    public function setName(string $name): NodeMeta
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return NodeMeta
     */
    public function setId(int $id): NodeMeta
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return NodeMeta
     */
    public function setKey(string $key): NodeMeta
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return NodeMeta
     */
    public function getParent(): ?NodeMeta
    {
        return $this->parent;
    }

    /**
     * @param NodeMeta $parent
     * @return NodeMeta
     */
    public function setParent(NodeMeta $parent = null): ?NodeMeta
    {
        $this->parent = $parent;

        //$parent->addChild($this);

        return $this;
    }

    /**
     * @return NodeMeta[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param NodeMeta[] $children
     * @return NodeMeta
     */
    public function setChildren(array $children): NodeMeta
    {
        $this->children = $children;
        return $this;
    }


    public function addChild(NodeMeta $child)
    {
        $this->children[] = $child;
        $child->setParent($this);
        return $this;
    }

    /**
     * @return NodeDefinition
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @param NodeDefinition $definition
     * @return NodeMeta
     */
    public function setDefinition(NodeDefinition $definition)
    {
        $this->definition = $definition;
        return $this;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     * @return NodeMeta
     */
    public function setTags(array $tags): NodeMeta
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @return string
     */
    public function getClassname(): string
    {
        return $this->classname;
    }

    /**
     * @param string $classname
     * @return NodeMeta
     */
    public function setClassname(string $classname): NodeMeta
    {
        $this->classname = $classname;
        return $this;
    }

    /**
     * @return string
     */
    public function getDtype(): string
    {
        return $this->dtype;
    }

    /**
     * @param string $dtype
     * @return NodeMeta
     */
    public function setDtype(string $dtype): NodeMeta
    {
        $this->dtype = $dtype;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return NodeMeta
     */
    public function setPosition(int $position): NodeMeta
    {
        $this->position = $position;
        return $this;
    }


    public function __toString()
    {
        return $this->id . ' (' .$this->name.')';
    }
}
<?php

namespace MandarinMedien\MMCmfNodeBundle\Configuration;

class NodeDefinition
{

    /**
     * @var
     */
    public $templates;

    /**
     * @var string
     */
    public $icon;


    /**
     * @var string
     */
    public $key;


    /**
     * @var NodeDefinition[]
     */
    public $children;


    /**
     * @var string
     */
    public $className;


    public function setKey(string $key) :NodeDefinition
    {
        $this->key = $key;
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
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     * @return NodeDefinition
     */
    public function setClassName(string $className): NodeDefinition
    {
        $this->className = $className;
        return $this;
    }


    /**
     * @return array
     */
    public function getTemplates(): ?array
    {
        return $this->templates;
    }

    /**
     * @param mixed $templates
     * @return NodeDefinition
     */
    public function setTemplates(?array $templates) :NodeDefinition
    {
        $this->templates = $templates;
        return $this;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return NodeDefinition
     */
    public function setIcon(string $icon): NodeDefinition
    {
        $this->icon = $icon;
        return $this;
    }


    /**
     * @return NodeDefinition[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param NodeDefinition[] $children
     * @return NodeDefinition
     */
    public function setChildren(?array $children): NodeDefinition
    {
        $this->children = $children;
        return $this;
    }


    /**
     * add a child definition
     *
     * @param string $className
     * @return $this
     */
    public function addChild(string $className) :NodeDefinition
    {
        $this->children[] = $className;
        return $this;
    }

}
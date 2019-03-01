<?php

namespace MandarinMedien\MMCmfNodeBundle\Configuration;


class TemplateDefinition
{

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $nodes = [];


    /**
     * @var string
     */
    protected $name;

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return TemplateDefinition
     */
    public function setPath(string $path): TemplateDefinition
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return array
     */
    public function getNodes(): ?array
    {
        return $this->nodes;
    }

    /**
     * @param array $nodes
     * @return TemplateDefinition
     */
    public function setNodes(?array $nodes): TemplateDefinition
    {
        $this->nodes = $nodes;
        return $this;
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
     * @return TemplateDefinition
     */
    public function setName(string $name): TemplateDefinition
    {
        $this->name = $name;
        return $this;
    }

}
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
    protected $tags = [];


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
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * @param array $nodes
     * @return TemplateDefinition
     */
    public function setTags(?array $tags): TemplateDefinition
    {
        $this->tags = $tags;
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
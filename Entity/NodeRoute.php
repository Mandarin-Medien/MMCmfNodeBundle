<?php

namespace MandarinMedien\MMCmfNodeBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use MandarinMedien\MMCmfNodeBundle\Validator\Constraint as RoutingAssert;

/**
 * NodeRoute
 * @RoutingAssert\NodeRouteUnique
 */
class NodeRoute implements NodeRouteInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var Node
     */
    protected $node;

    /**
     * @Assert\NotBlank
     */
    protected $route;


    /**
     * @var array
     */
    protected $domains;


    public function __construct()
    {
        $this->domains = [];
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
     * Set route
     *
     * @param string $route
     *
     * @return NodeRoute
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    public function addDomain(string $domain)
    {
        if(!in_array($domain, $this->domains))
            $this->domains[] = $domain;

        return $this;

    }

    public function getDomains()
    {
        return $this->domains;
    }

    public function removeDomain(string $domain)
    {
        if(false !== ($index = array_search($domain, $this->domains))) {
            unset($this->domains[$index]);
            $this->domains = array_values($this->domains);
        }

        return $this;
    }


    public function __toString()
    {
        return $this->getRoute();
    }
}


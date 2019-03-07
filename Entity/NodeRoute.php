<?php

namespace MandarinMedien\MMCmfNodeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use MandarinMedien\MMCmfNodeBundle\Validator\Constraint as RoutingAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * NodeRoute
 * @RoutingAssert\NodeRouteUnique
 * @ORM\Entity()
 * @ORM\Table(indexes={@ORM\Index(name="uri_index",columns={"route"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorMap({"default"= "NodeRoute","auto"= "AutoNodeRoute","alias"= "AliasNodeRoute","redirect"= "RedirectNodeRoute","external"= "ExternalNodeRoute"})
 */
class NodeRoute implements NodeRouteInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @var string
     */
    protected $id;

    /**
     * @Assert\NotBlank
     * @ORM\Column(nullable=false)
     */
    protected $route;

    /**
     * @var NodeInterface
     */
    protected $node;

    /**
     * @var array|NodeRouteDomainInterface[]
     * @ORM\ManyToMany(targetEntity="MandarinMedien\MMCmfNodeBundle\Entity\NodeRouteDomainInterface")
     */
    protected $domains;

    public function __construct()
    {
        $this->domains = new ArrayCollection();
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

    /**
     * @return NodeRouteDomainInterface[]|array
     */
    public function getDomains()
    {
        return $this->domains;
    }


    /**
     * @param NodeRouteDomainInterface $domain
     * @return $this
     */
    public function addDomain(NodeRouteDomainInterface $domain)
    {
        $this->domains->add($domain);

        return $this;
    }

    /**
     * @param NodeRouteDomainInterface $domain
     * @return $this
     */
    public function removeDomain(NodeRouteDomainInterface $domain)
    {
        $this->domains->removeElement($domain);

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getRoute();
    }
}


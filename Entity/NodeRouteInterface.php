<?php

namespace MandarinMedien\MMCmfNodeBundle\Entity;

/**
 * Interface NodeRouteInterface
 * @package MandarinMedien\MMCmfNodeBundle\Entity
 */
interface NodeRouteInterface
{
    /**
     * @param string$route
     * @return mixed
     */
    public function setRoute($route);

    /**
     * @return string
     */
    public function getRoute();

    /**
     * @param NodeRouteDomainInterface $domain
     * @return $this
     */
    public function addDomain(NodeRouteDomainInterface $domain);

    /**
     * @return array|NodeRouteDomainInterface[]
     */
    public function getDomains();

    /**
     * @param NodeRouteDomainInterface $domain
     * @return $this
     */
    public function removeDomain(NodeRouteDomainInterface $domain);
}


<?php

namespace MandarinMedien\MMCmfNodeBundle\Entity;

use MandarinMedien\MMCmfNodeBundle\Entity\Node;


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
     * @param string $domain
     * @return $this
     */
    public function addDomain(string $domain);

    /**
     * @return array
     */
    public function getDomains();

    /**
     * @param string $domain
     * @return $this
     */
    public function removeDomain(string $domain);
}


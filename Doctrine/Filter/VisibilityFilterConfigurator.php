<?php

namespace MandarinMedien\MMCmfNodeBundle\Doctrine\Filter;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\Common\Annotations\Reader;

/**
 * Class VisibilityFilterConfigurator
 * @package MandarinMedien\MMCmfNodeBundle\Doctrine\Filter
 */
class VisibilityFilterConfigurator
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;
    /**
     * @var array
     */
    protected $roles;
    /**
     * @var string
     */
    protected $name = 'visibility_filter';

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->roles = array();
    }

    public function onKernelRequest()
    {

        if($this->em->getFilters()->isEnabled($this->name)) {
            /**
             * @var $filter VisibilityFilter
             */
            $filter = $this->em->getFilters()->getFilter($this->name);


            if ($user = $this->getUser()) {
                $filter
                    ->setRoles($this->roles)
                    ->setUser($this->getUser());
            }
        }
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return mixed|null
     */
    private function getUser()
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return null;
        }

        $user = $token->getUser();

        if (!($user instanceof UserInterface)) {
            return null;
        }

        return $user;
    }
}
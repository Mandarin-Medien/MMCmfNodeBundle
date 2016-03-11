<?php

namespace MandarinMedien\MMCmfNodeBundle\Doctrine\Filter;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Annotations\Reader;

class VisibilityFilterConfigurator
{
    protected $em;
    protected $tokenStorage;
    protected $roles;
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
            $filter = $this->em->getFilters()->getFilter($this->name);


            if ($user = $this->getUser()) {
                $filter
                    ->setRoles($this->roles)
                    ->setUser($this->getUser());
            }
        }
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

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
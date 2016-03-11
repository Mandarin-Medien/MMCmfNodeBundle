<?php

namespace MandarinMedien\MMCmfNodeBundle\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use FOS\UserBundle\Model\UserInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;



class VisibilityFilter extends SQLFilter
{

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var array
     */
    protected $roles;


    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if($targetEntity->reflClass->implementsInterface(NodeInterface::class)) {

            if($this->getUser() && count(array_intersect($this->roles, $this->getUser()->getRoles())) > 0) {
                return "";
            }

            return $targetTableAlias.'.visible = 1';
        }

        return "";
    }


    /**
     * @param UserInterface $user
     * @return $this
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * get the roles
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }


    /**
     * @return UserInterface
     */
    protected function getUser()
    {
        return $this->user;
    }
}

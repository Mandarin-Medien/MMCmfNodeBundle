<?php

namespace MandarinMedien\MMCmfNodeBundle\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Symfony\Component\Security\Core\User\UserInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;


/**
 * Class VisibilityFilter
 *
 * This Filter hides all instances of NoderInterface
 * which are hidden by visible property
 *
 * @package MandarinMedien\MMCmfNodeBundle\Doctrine\Filter
 */
class VisibilityFilter extends SQLFilter
{

    /**
     * @param ClassMetadata $targetEntity
     * @param string $targetTableAlias
     * @return string
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        return $targetEntity->reflClass->implementsInterface(NodeInterface::class)
            ? $targetTableAlias.'.visible = 1'
            : "";
    }
}

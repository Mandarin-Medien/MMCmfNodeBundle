<?php

namespace MandarinMedien\MMCmfNodeBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use MandarinMedien\MMCmfNodeBundle\Model\BaseNode;
use MandarinMedien\MMCmfNodeBundle\Validator\Constraint as NodeAssert;

/**
 * Node
 * @NodeAssert\NoNodeRecursion
 * @ORM\Entity()
 * @ORM\InheritanceType("JOINED")
 */
class Node extends BaseNode
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @var string
     */
    protected $id;

    /**
     * @ORM\Column(nullable=false)
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Sortable(groups={"parent"})
     * @var integer
     */
    protected $position = 0;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    protected $visible = true;

    /**
     * @ORM\OneToMany(targetEntity=Node::class, mappedBy="parent", cascade={"all"})
     * @ORM\OrderBy({"position": "ASC"})
     * @var Collection|Node[]
     */
    protected $nodes;

    /**
     * @ORM\ManyToOne(targetEntity=Node::class, inversedBy="nodes")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @var Node
     */
    protected $parent;
}

<?php

namespace MandarinMedien\MMCmfNodeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

interface NodeInterface
{
    public function addNode(NodeInterface $node);

    public function setNodes(ArrayCollection $nodes);

    public function removeNode(NodeInterface $node);

    public function getNodes();

    public function setName($name);

    public function getName();

    public function getId();

    public function setParent(NodeInterface $node);

    public function getParent();
}



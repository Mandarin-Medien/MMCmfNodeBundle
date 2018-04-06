<?php

namespace MandarinMedien\MMCmfNodeBundle\Entity;


interface NodeInterface
{
    public function addNode(NodeInterface $node);

    public function removeNode(NodeInterface $node);

    public function getNodes();

    public function setName($name);

    public function getName();

    public function getId();

    public function setParent(NodeInterface $node);

    public function getParent();

    public function setPosition($position);

    public function getPosition();

    public function isVisible();

    public function setVisible($visible);
}



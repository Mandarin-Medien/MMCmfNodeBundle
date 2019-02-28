<?php

namespace MandarinMedien\MMCmfNodeBundle\Entity;

interface TemplatableNodeInterface extends NodeInterface
{

    public function setTemplate($template);

    public function getTemplate();

}
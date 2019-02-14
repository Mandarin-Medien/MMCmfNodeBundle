<?php

namespace MandarinMedien\MMCmfNodeBundle\Entity;

interface TemplatableNodeInterface
{

    public function setTemplate($template);

    public function getTemplate();

}
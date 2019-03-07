<?php

namespace MandarinMedien\MMCmfNodeBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait TemplatableNodeTrait
{
    /**
     * @ORM\Column(nullable=true)
     * @var string
     */
    protected $template;

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

}
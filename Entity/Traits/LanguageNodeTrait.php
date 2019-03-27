<?php

namespace MandarinMedien\MMCmfNodeBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait LanguageNodeTrait
{

    /**
     * @ORM\Column(nullable=false,length=5)
     * @Assert\Locale()
     * @var string
     */
    protected $locale;

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param $locale
     * @return void
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

}

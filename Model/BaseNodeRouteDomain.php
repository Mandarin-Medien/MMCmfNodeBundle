<?php
/**
 * Created by PhpStorm.
 * User: tonigurski
 * Date: 2019-03-07
 * Time: 09:53
 */

namespace MandarinMedien\MMCmfNodeBundle\Model;


use MandarinMedien\MMCmfNodeBundle\Entity\NodeRouteDomainInterface;
use Symfony\Component\Validator\Constraints as Assert;

class BaseNodeRouteDomain implements NodeRouteDomainInterface
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

}
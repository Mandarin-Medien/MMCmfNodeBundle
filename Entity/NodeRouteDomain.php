<?php
/**
 * Created by PhpStorm.
 * User: tonigurski
 * Date: 2019-03-07
 * Time: 09:57
 */

namespace MandarinMedien\MMCmfNodeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MandarinMedien\MMCmfNodeBundle\Model\BaseNodeRouteDomain;

/**
 * Class NodeRouteDomain
 * @package MandarinMedien\MMCmfNodeBundle\Entity
 * @ORM\Entity()
 */
class NodeRouteDomain extends BaseNodeRouteDomain
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @var string
     */
    protected $id;

    /**
     * @ORM\Column(nullable=false,unique=true)
     * @var string
     */
    protected $name;
}
<?php
/**
 * Created by PhpStorm.
 * User: tonigurski
 * Date: 2019-03-07
 * Time: 09:49
 */

namespace MandarinMedien\MMCmfNodeBundle\Entity;


interface NodeRouteDomainInterface
{
    public function getName(): string;
    public function __toString(): string;
}
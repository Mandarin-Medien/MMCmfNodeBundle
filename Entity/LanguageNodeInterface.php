<?php
/**
 * Created by PhpStorm.
 * User: tonigurski
 * Date: 2019-02-28
 * Time: 17:41
 */

namespace MandarinMedien\MMCmfNodeBundle\Entity;

interface LanguageNodeInterface extends RoutableNodeInterface
{
    /**
     * needs to return an valid _locale value
     *
     * @return string
     */
    public function getLocale(): string;
}
<?php
/**
 * Created by PhpStorm.
 * User: tonigurski
 * Date: 2019-03-07
 * Time: 16:37
 */

namespace MandarinMedien\MMCmfNodeBundle\Event;

final class NodeControllerEvents
{
    const BEFORE_RESOLVING = 'mm.node.before_resolving';
    const AFTER_RESOLVING = 'mm.node.after_resolving';
    const BEFORE_REDIRECT = 'mm.node.before_redirect';
    const BEFORE_RENDER = 'mm.node.before_render';
    const AFTER_RENDER = 'mm.node.after_render';
    const BEFORE_RESPONSE = 'mm.node.before_response';
    const BEFORE_NOT_FOUND = 'mm.node.before_not_found';
}
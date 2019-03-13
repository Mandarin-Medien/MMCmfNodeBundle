<?php
/**
 * Created by PhpStorm.
 * User: tonigurski
 * Date: 2019-03-13
 * Time: 13:37
 */

namespace MandarinMedien\MMCmfNodeBundle\Exception;

use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRouteInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NoTemplatableNodeFoundException extends NotFoundHttpException
{
    /**
     * @var NodeRouteInterface
     */
    private $nodeRoute;
    /**
     * @var NodeInterface
     */
    private $node;

    /**
     * @param string $message The internal exception message
     * @param \Exception $previous The previous exception
     * @param int $code The internal exception code
     * @param NodeInterface $node
     * @param NodeRouteInterface $nodeRoute
     */
    public function __construct($message = null, \Exception $previous = null, $code = 0, NodeInterface $node, NodeRouteInterface $nodeRoute = null)
    {
        parent::__construct(404, $message, $previous, [], $code);
        $this->nodeRoute = $nodeRoute;
        $this->node = $node;
    }
}
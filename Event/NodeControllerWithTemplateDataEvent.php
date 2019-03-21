<?php
/**
 * Created by PhpStorm.
 * User: tonigurski
 * Date: 2019-03-07
 * Time: 16:37
 */

namespace MandarinMedien\MMCmfNodeBundle\Event;

use Doctrine\Common\Collections\ArrayCollection;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRouteInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NodeControllerWithTemplateDataEvent extends NodeControllerWithNodeEvent
{
    /**
     * @var array
     */
    private $templateData;

    /**
     * @var string
     */
    private $templateFile;

    /**
     * NodeControllerWithResponseEvent constructor.
     *
     * @param Request $request
     * @param NodeRouteInterface $nodeRoute
     * @param NodeInterface $node
     * @param array $templateData
     * @param string $templateFile
     */
    public function __construct(Request $request, NodeRouteInterface $nodeRoute, NodeInterface $node, array &$templateData, string &$templateFile)
    {
        parent::__construct($request, $nodeRoute, $node);

        $this->templateData = &$templateData;
        $this->templateFile = &$templateFile;
    }

    /**
     * @return array
     */
    public function &getTemplateData()
    {
        return $this->templateData;
    }

    /**
     * @return string
     */
    public function &getTemplateFile(): string
    {
        return $this->templateFile;
    }
}
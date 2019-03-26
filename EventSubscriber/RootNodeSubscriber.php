<?php

namespace MandarinMedien\MMCmfNodeBundle\EventSubscriber;

use MandarinMedien\MMCmfNodeBundle\Factory\NodeMeta;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use MandarinMedien\MMCmfNodeBundle\Event\NodeControllerEvents;
use MandarinMedien\MMCmfNodeBundle\Event\NodeControllerWithTemplateDataEvent;
use MandarinMedien\MMCmfNodeBundle\Factory\NodeFactory;

/**
 * Class RootNodeSubscriber
 * @package MandarinMedien\MMCmfNodeBundle\EventSubscriber
 *
 * This Subscriber injects the first level root node from current node
 *
 */
class RootNodeSubscriber implements EventSubscriberInterface
{

    /**
     * @var NodeFactory
     */
    protected $factory;


    /**
     * RootNodeSubscriber constructor.
     * @param NodeFactory $factory
     */
    public function __construct(NodeFactory $factory)
    {
        $this->factory = $factory;
    }


    /**
     * @param NodeControllerWithTemplateDataEvent $event
     */
    public function setRootNode(NodeControllerWithTemplateDataEvent $event)
    {

        $root = $meta = $this->factory->getNodeMeta($event->getNode());

        // traverse up the tree to get the root node
        while($meta = $meta->getParent()) $root = $meta;


        $templateData = &$event->getTemplateData();
        $templateData['root'] = $root->load();;
    }


    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            NodeControllerEvents::BEFORE_RENDER => ['setRootNode']
        ];
    }

}

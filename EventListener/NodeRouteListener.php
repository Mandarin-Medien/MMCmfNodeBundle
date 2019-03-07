<?php

namespace MandarinMedien\MMCmfNodeBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use MandarinMedien\MMCmfNodeBundle\Entity\AutoNodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\RoutableNodeInterface;
use MandarinMedien\MMCmfNodeBundle\Manager\NodeRouteManager;
use Psr\Container\ContainerInterface;


/**
 * Class NoteRouteListener
 *
 * handles the creation and updates of Node related NodeRoute Entites
 *
 * @package MandarinMedien\MMCmfNodeBundle\EventListener
 */
class NodeRouteListener
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * prePersist Event
     * whenever a new Node is created, also create automatically a new NodeRoute
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {

        // update child routes
        $entity = $args->getEntity();

        if (    $entity instanceof RoutableNodeInterface
            &&  $entity->hasAutoNodeRouteGeneration()
        ) {
            $routeManager = $this->container->get(NodeRouteManager::class);
            $entity->addRoute($routeManager->generateAutoNodeRoute($entity));
        }

        return;
    }


    /**
     * onFlush Event for persisting all NodeRoutes that needs an update
     *
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $unit = $args->getEntityManager()->getUnitOfWork();
        $routeManager = $this->container->get(NodeRouteManager::class);


        foreach($unit->getScheduledEntityUpdates() as $entity) {

            if(     $entity instanceof RoutableNodeInterface
                &&  $entity->hasAutoNodeRouteGeneration()
            ) {

                $hasNodeRoute = false;
                $routeGenerated = false;

                foreach ($entity->getRoutes() as $route) {
                    if($route instanceof AutoNodeRoute) {
                        $hasNodeRoute = true;
                        break;
                    }
                }

                if(!$hasNodeRoute) {
                    $entity->addRoute($routeManager->generateAutoNodeRoute($entity));
                    $routeGenerated = true;
                }


                // check if Node::name has changed
                $changed = $unit->getEntityChangeSet($entity);

                if(     array_key_exists('name', $changed)
                    ||  array_key_exists('parent', $changed)
                    ||  $routeGenerated
                ) {

                    // update all child AutoNodeRoutes
                    $routeManager->getAutoNodeRoutesRecursive($entity);
                    $unit->computeChangeSets();
                }
            }
        }
    }


    public function postFlush(PostFlushEventArgs $args)
    {



        $router = $this->container->get('router');
        $cache_dir = $this->container->getParameter('kernel.cache_dir');

        $this->container->get('cache_clearer')->clear($cache_dir);
        //$router->warmUp($cache_dir);
    }
}
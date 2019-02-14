<?php

namespace MandarinMedien\MMCmfNodeBundle\Resolver;

use Doctrine\ORM\EntityManagerInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\Node;
use MandarinMedien\MMCmfNodeBundle\Factory\NodeFactory;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRouteInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\RoutableNodeInterface;


/**
 * Class NodeResolver
 *
 * This class loads RoutableNodeInterfaces by a given NodeRouteInterface
 *
 * @package MandarinMedien\MMCmfNodeBundle\Resolver
 */
class NodeResolver
{

    /**
     * @var EntityManagerInterface
     */
    protected $manager;


    /**
     * @var array
     */
    protected $classes;

    /**
     * @var NodeFactory
     */
    protected $factory;


    /**
     * NodeResolver constructor.
     * @param EntityManagerInterface $manager
     * @param NodeFactory $factory
     */
    public function __construct(EntityManagerInterface $manager, NodeFactory $factory)
    {
        $this->factory = $factory;
        $this->manager = $manager;
        $this->classes = $this->getRoutableNodeClasses();
    }


    /**
     * loads the Node by the given NodeRouteInterface
     * @param NodeRouteInterface $route
     * @return mixed|null
     */
    public function resolve(NodeRouteInterface $route)
    {

        foreach($this->classes as $class) {
            $qb = $this->manager->createQueryBuilder()
                ->select('n')
                ->from($class, 'n')
                ->join('n.routes', 'r')
                ->where('r.id = :route')
                ->setParameter('route', $route->getId());

            $node = $qb->getQuery()->getResult();

            if($node) return $node[0];
        }

        return null;
    }


    /**
     * builds a list of all classes implementing RoutableNodeInterface
     * @return array
     */
    protected function getRoutableNodeClasses()
    {
        $metadata = $this->manager->getClassMetadata($this->factory->getRootClass());
        $routables = array_values(array_filter($metadata->subClasses, function($subNode) {
            $reflection = new \ReflectionClass($subNode);
            return $reflection->implementsInterface(RoutableNodeInterface::class);
        }));

        return $routables;
    }

}
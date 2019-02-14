<?php

namespace MandarinMedien\MMCmfNodeBundle\Factory;

use Doctrine\ORM\EntityManager;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRouteInterface;

/**
 * Class NodeRouteFactory
 * @package MandarinMedien\MMCmfNodeBundle\Entity
 */
class NodeRouteFactory
{

    private $manager;
    private $factory_class = NodeRoute::class;
    private $meta;


    /**
     * NodeRouteFactory constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
        $this->meta = $this->manager->getClassMetadata(
            $this->factory_class
        );
    }


    /**
     * create a new NodeRoute instance by discriminator value
     * @param string $discriminator
     * @return NodeRouteInterface
     * @throws \Exception
     */
    public function createNodeRoute($discriminator = 'default')
    {
        $reflection = new \ReflectionClass($this->getClassByDiscriminator($discriminator));
        return $reflection->newInstance();
    }


    /**
     * get all available discriminator values of NodeRoute entity
     * @param array $exclude exclude specific discriminators
     * @return array
     */
    public function getDiscriminators($exclude = array('default', 'auto'))
    {
        return array_diff(array_keys($this->meta->discriminatorMap), $exclude);
    }


    /**
     * get the discriminator value by the given instance
     * @param NodeRouteInterface $nodeRoute
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getDiscriminatorByClass(NodeRouteInterface $nodeRoute)
    {
        return $this->manager->getClassMetadata(get_class($nodeRoute))->discriminatorValue;
    }


    /**
     * get the NodeRoute subclass by discriminator value
     * @param string $discriminator
     * @return NodeRouteInterface
     * @throws \Exception
     */
    protected function getClassByDiscriminator($discriminator)
    {
        if($class = ($this->meta->discriminatorMap[$discriminator])) {
            return $class;
        } else {
            throw new \Exception('class not found');
        }
    }
}
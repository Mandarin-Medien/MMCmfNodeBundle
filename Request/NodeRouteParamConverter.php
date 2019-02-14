<?php

namespace MandarinMedien\MMCmfNodeBundle\Request;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRoute;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class NodeRouteParamConverter implements ParamConverterInterface
{

    private $manager;
    private $repositoryClass = NodeRoute::class;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    function apply(Request $request, ParamConverter $configuration)
    {

        $route = $request->attributes->get('route');

        if($route !== null && !is_null(
            $route = $this->manager
                ->getRepository($this->repositoryClass)
                ->findOneBy(
                    array(
                        'route' => '/'.$route
                    )
                )
        )) {

            $request->attributes->add(
                array($configuration->getName() => $route)
            );
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() == $this->repositoryClass;
    }
}
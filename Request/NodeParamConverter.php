<?php

namespace MandarinMedien\MMCmfNodeBundle\Request;

use Doctrine\ORM\EntityManagerInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use MandarinMedien\MMCmfNodeBundle\Factory\NodeFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NodeParamConverter implements ParamConverterInterface
{


    /**
     * @var NodeFactory
     */
    protected $nodeFactory;


    /**
     * @var EntityManagerInterface
     */
    protected $manager;


    /**
     * NodeParamConverter constructor.
     * @param NodeFactory $nodeFactory
     */
    public function __construct(NodeFactory $nodeFactory, EntityManagerInterface $manager)
    {
        $this->nodeFactory = $nodeFactory;

        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    function apply(Request $request, ParamConverter $configuration)
    {

        $name = $configuration->getName();

        if($node = $this->manager->getRepository($this->nodeFactory->getRootClass())->find((int) $request->attributes->get('id'))) {
            $request->attributes->set($name, $node);
        } else {
            throw new NotFoundHttpException('node not found');
        }
    }


    /**
     * {@inheritdoc}
     */
    function supports(ParamConverter $configuration)
    {
        dump('supports');

        if($configuration->getClass()) {
            return $configuration->getClass() === NodeInterface::class;
        }

        return false;
    }

}
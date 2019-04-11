<?php

namespace MandarinMedien\MMCmfNodeBundle\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use MandarinMedien\MMCmfNodeBundle\Doctrine\Filter\VisibilityFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class VisibilityFilterConfigurator
 * @package MandarinMedien\MMCmfNodeBundle\Doctrine\Filter
 */
class VisibilityFilterSubscriber implements EventSubscriberInterface
{


    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var Security
     */
    private $security;


    /**
     * VisibilityFilterSubscriber constructor.
     * @param EntityManagerInterface $em
     * @param Security $security
     */
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;

        $this->security = $security;
    }


    /**
     * disable filter when logged in
     */
    public function onKernelRequest()
    {
        $filters = $this->em->getFilters();

        if($filters->isEnabled(VisibilityFilter::class) && $this->security->isGranted(["ROLE_ADMIN", "ROLE_USER"]) )
            $filters->disable(VisibilityFilter::class);

    }


    /**
     * @return array
     */
    static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ["onKernelRequest", 1]
            ]
        ];

    }
}
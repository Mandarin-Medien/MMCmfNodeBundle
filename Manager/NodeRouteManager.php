<?php

namespace MandarinMedien\MMCmfNodeBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use MandarinMedien\MMCmfNodeBundle\Entity\AutoNodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\Node;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\RoutableNodeInterface;
use utilphp\util;

/**
 * Class NodeRouteManager
 *
 * Handles the generating, persisting and updating of NodeRoutes
 *
 * @package MandarinMedien\MMCmfRoutingBundle\Entity
 */
class NodeRouteManager
{

    /**
     * @var EntityManagerInterface
     */
    protected $manager;


    /**
     * NodeRouteManager constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    /**
     * generates a AutoNodeRoute for a given Node
     *
     * @param RoutableNodeInterface $node
     * @return AutoNodeRoute
     */
    public function generateAutoNodeRoute(RoutableNodeInterface $node)
    {


        $route = (new AutoNodeRoute())
            ->setRoute('/' . $this->slugify($node));

        /**
         * @var Node $parent
         */
        while (!is_null($parent = $node->getParent()) && $parent instanceof RoutableNodeInterface) {
            $route->setRoute($this->getAutoNodeRoute($parent) . $route->getRoute());
            $node = $parent;
        }

        // add increment if route exists
        $route->setRoute(
            $route->getRoute().$this->getIncrement($route->getRoute())
        );

        return $route;
    }


    /**
     * get updated AutoNodeRoutes recursive
     *
     * @param NodeInterface $node
     * @param null|string $base
     * @return NodeRoute[]
     */
    public function getAutoNodeRoutesRecursive(NodeInterface &$node, $base = null, &$routeObjects = array())
    {

        $route = $this->getAutoNodeRoute($node);

        if($route) {


            $route->setRoute(
                $base ? $base . '/' . util::slugify($node->getName())
                    : $this->generateAutoNodeRoute($node)->getRoute()
            );

            // add soft Increment
            $route->setRoute($route->getRoute().$this->softIncrement($route, $routeObjects));


            $routeObjects[] = $route;


            // recursive loop
            foreach ($node->getNodes() as $subnode) {
                if($subnode instanceof RoutableNodeInterface)
                    $this->getAutoNodeRoutesRecursive($subnode, $this->getAutoNodeRoute($node)->getRoute(), $routeObjects);
            }
        }


        return $routeObjects;
    }


    /**
     * generate an url-friendly string from Node::name
     * @param Node $node
     * @return string
     */
    public function slugify(NodeInterface $node)
    {
        return util::slugify($node->getName());
    }


    /**
     * return the AutoNodeRoute::route of the given Node entity
     * @param NodeInterface $node
     * @return AutoNodeRoute
     */
    protected function getAutoNodeRoute(RoutableNodeInterface $node)
    {
        foreach($node->getRoutes() as $route) {
            if($route instanceof AutoNodeRoute) {
                return $route;
            }
        }
    }


    /**
     * check if the generated route already exists and return the count
     * @param string $route route string to check
     * @return string increment value in parenthesis
     */
    protected function getIncrement($route)
    {

       // var_dump($route);

        $mapper = new ResultSetMapping();
        $mapper->addScalarResult('route', 'route');


        $builder = $this->manager->createNativeQuery(
            'SELECT route FROM node_route WHERE route REGEXP ?',
            $mapper
        )
        ->setParameter('1', $this->prepareMysqlRegexp($route));

        //var_dump($builder->getArrayResult());


        $i = 0;
        foreach($builder->getArrayResult() as $_route)
        {

            if(preg_match('#\(([0-9])*\)$#', $_route['route'], $matches)) {
                $i = (int) $matches[1] == $i  ? (int) $matches[1]+1 : $i;
            } else {
                $i++;
            }
        }

        return ($i ? "($i)" : '');
    }


    /**
     * get increment values based on given NodeRoute[]
     * @param AutoNodeRoute $route
     * @param AutoNodeRoute[] $routes
     * @return string increment value in parenthesis
     */
    public function softIncrement($route, array $routes)
    {

        // flat the route array
        $flatten = array_map(function($route) {
            return $route->getRoute();
        }, $routes);

        $i = 0;

        array_walk($routes, function($_route) use($route, &$i) {

            $search = "#".addcslashes(preg_replace('/\([0-9]+\)*$/', "", $route),'()').'(\([0-9]+\))*$#';

            if(preg_match($search, $_route->getRoute(), $matches)) {
                if(count($matches) == 2) {
                    $i = (int)$matches[1] == $i ? (int)$matches[1] + 1 : $i;
                } else {
                    $i++;
                }
            };
        });

        return ($i ? "($i)" : '');
    }


    /**
     * create regexp for mysql query
     * @param $string NodeRoute::route
     * @return string
     */
    protected function prepareMysqlRegexp($string)
    {
        $regexp = $string;

        // first remove trailing increment
        $regexp = preg_replace('/\([0-9]\)$/', "", $regexp);

        // escape existing parenthesis
        $regexp = preg_replace('(\(|\))', '\\\\${0}', $regexp);

        // append matching mysql matching pattern
        $regexp .= "([[.left-parenthesis.]][0-9]*[[.right-parenthesis.]])*$";

        return $regexp;

    }


}
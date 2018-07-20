<?php

namespace MandarinMedien\MMCmfNodeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class MMCmfNodeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');


        // process
        $nodeFactory = $container->getDefinition('mm_cmf_node.factory');

        if($config['nodes']) {

            $nodeFactory
                ->addMethodCall("setDefaultIcon", [$config['defaultIcon']])
                ->addMethodCall("setRootClass", [$config['class']]);

            foreach($config['nodes'] as $class => $subConfig) {

                if($subConfig['icon']) {
                    $nodeFactory->addMethodCall("addIcon", array(
                        $class, $subConfig['icon']
                    ));
                }

                if($subConfig['children']) {
                    foreach ($subConfig['children'] as $childClass) {
                        $nodeFactory->addMethodCall("addChildNodeDefinition", array(
                            $class,
                            $childClass
                        ));
                    }
                }
            }
        }

    }
}

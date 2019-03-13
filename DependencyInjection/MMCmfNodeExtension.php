<?php

namespace MandarinMedien\MMCmfNodeBundle\DependencyInjection;

use MandarinMedien\MMCmfNodeBundle\Factory\NodeFactory;
use MandarinMedien\MMCmfNodeBundle\Templating\TemplateManager;
use MandarinMedien\MMCmfNodeBundle\Configuration\NodeDefinition;
use MandarinMedien\MMCmfNodeBundle\Configuration\NodeRegistry;
use MandarinMedien\MMCmfNodeBundle\Configuration\TagRegistry;
use MandarinMedien\MMCmfNodeBundle\Configuration\TemplateDefinition;
use MandarinMedien\MMCmfNodeBundle\Resolver\NodeDefinitionResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

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

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');


        // process
        $nodeFactory = $container->getDefinition(NodeFactory::class);
        $nodeRegistry = $container->getDefinition(NodeRegistry::class);
        $tagRegistry = $container->getDefinition(TagRegistry::class);
        $templateManager = $container->getDefinition(TemplateManager::class);
        $templateManager->setArgument('$overrideDir', $config['templating']['override_dir']);


        $nodeFactory
            ->addMethodCall("setDefaultIcon", [$config['defaultIcon']])
            ->addMethodCall("setRootClass", [$config['class']]);


        foreach ($config['tags'] as $tag => $id)
            $tagRegistry->addMethodCall('add', [$id, $tag]);


        // build the node hierarchy configuration
        foreach ($nodeDefinitions = $this->buildNodeDefinitions($config['nodes'], $config['defaultIcon']) as $key => $nodeDefinition)
            $nodeRegistry->addMethodCall('add', [$key, (new Definition())
                ->setClass(NodeDefinition::class)
                ->addMethodCall('setIcon', [$nodeDefinition['icon']])
                ->addMethodCall('setKey', [$nodeDefinition['key']])
                ->addMethodCall('setClassName', [$nodeDefinition['className']])
                ->addMethodCall('setChildren', [$nodeDefinition['children']])
                ->addMethodCall('setTemplates', [array_map(function ($template) {
                    return (new Definition())
                        ->setClass(TemplateDefinition::class)
                        ->addMethodCall('setName', [$template['name']])
                        ->addMethodCall('setPath', [$template['path']])
                        ->addMethodCall('setTags', [$template['tags']]);
                }, $nodeDefinition['templates'])])
            ]);
    }


    public function buildNodeDefinitions($config, $defaultIcon, $definitions = [], $parent = null)
    {

        // at first, collect and register the global definitions
        foreach ($config as $className => $classConfig) {

            $key = $parent
                ? ($parent . NodeDefinitionResolver::KEY_SEPARATOR . $className)
                : $className;

            $definition = [
                'className' => $className,
                'key' => $key,
                'icon' => $defaultIcon,
                'templates' => [],
                'children' => [],
            ];

            $pieces = array_reverse(explode(NodeDefinitionResolver::KEY_SEPARATOR, $key));
            array_pop($pieces);

            while ($piece = current($pieces)) {

                $search = isset($search)
                    ? ($piece . NodeDefinitionResolver::KEY_SEPARATOR . $search)
                    : $piece;

                $definition = $definitions[$search] ?? $definition;
                next($pieces);
            }


            if (is_array($classConfig)) {

                $definition['key'] = $key;
                $definition['icon'] = $classConfig['icon'] ?? $definition['icon'];
                $definition['templates'] = $classConfig['templates'] ?? $definition['templates'];
                $definition['children'] = array_key_exists('children', $classConfig) && is_array($classConfig['children'])
                    ? array_keys($classConfig['children'])
                    : $definition['children'];

                $definitions[$key] = $definition;

                // go to next level
                if (isset($classConfig['children']))
                    $definitions += $this->buildNodeDefinitions($classConfig['children'], $defaultIcon, $definitions, $key);
            }
        }

        return $definitions;
    }
}

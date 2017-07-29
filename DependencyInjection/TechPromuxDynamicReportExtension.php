<?php

namespace  TechPromux\DynamicReportBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class TechPromuxDynamicReportExtension extends Extension implements \Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface
{

    /**
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('config.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }


    //-------------------------------------------------------------


    /**
     * Configure the report page templates.
     *
     * @param ContainerBuilder $container Container builder
     * @param array            $config    Array of configuration
     */
    public function configureTemplates(ContainerBuilder $container, array $config) {

        // adding automatically all component services definitions to report manager
        if (false === $container->hasDefinition('tech_prommux_dynamic_report.manager.report')) {
            return;
        }

        $report_manager_definition = $container->getDefinition('tech_prommux_dynamic_report.manager.report');

        // add all templates to manager

        $report_manager_definition->addMethodCall('setTemplates', array($config['templates']));

        // set default template
        $report_manager_definition->addMethodCall('setDefaultTemplateCode', array($config['default_template']));
    }


}

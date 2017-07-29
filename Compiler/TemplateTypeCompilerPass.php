<?php

namespace TechPromux\DynamicReportBundle\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class TemplateTypeCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('techpromux_dynamic_report.manager.util_dynamic_report')) {
            return;
        }

        //$container->getDefinition('twig.loader')
        //    ->addMethodCall('addPath',
        //        array(__DIR__ . '/../Resources/views/' => 'TechPromuxDynamicReportBundle'));

        //------------------------
        // adding templates

    }

}

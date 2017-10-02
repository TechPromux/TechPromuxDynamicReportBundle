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

        $managerDefinitionId = 'techpromux_dynamic_report.manager.util_dynamic_report';

        $managerDefinition = $container->getDefinition(
            $managerDefinitionId
        );

        $taggedServicesIds = $container->findTaggedServiceIds(
            'techpromux_dynamic_report.type.template'
        );

        foreach ($taggedServicesIds as $id => $tags) {

            //$templateTypeDefinition = $container->getDefinition($id);

            $managerDefinition->addMethodCall(
                'addTemplateType',
                array(new \Symfony\Component\DependencyInjection\Reference($id))
            );
        }

        //------------------------
        // adding templates folder path

        $container->getDefinition('twig.loader.filesystem')
            ->addMethodCall('addPath',
                array(__DIR__ . '/../Resources/views/', 'TechPromuxDynamicReportBundle'));

        //-----------------------------

    }

}

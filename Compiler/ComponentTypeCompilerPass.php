<?php

namespace TechPromux\DynamicReportBundle\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ComponentTypeCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('techpromux_dynamic_report.manager.util_dynamic_report')) {
            return;
        }

        $templatingDefinitionId = 'templating';

        $managerDefinitionId = 'techpromux_dynamic_report.manager.util_dynamic_report';

        $managerDefinition = $container->getDefinition(
            $managerDefinitionId
        );

        $taggedServicesIds = $container->findTaggedServiceIds(
            'techpromux_dynamic_report.type.component'
        );

        foreach ($taggedServicesIds as $id => $tags) {

            $componentTypeDefinition = $container->getDefinition($id);

            $componentTypeDefinition->addMethodCall(
                'setTemplating',
                array(new \Symfony\Component\DependencyInjection\Reference($templatingDefinitionId))
            );

            $componentTypeDefinition->addMethodCall(
                'setUtilDynamicReportManager',
                array(new \Symfony\Component\DependencyInjection\Reference($managerDefinitionId))
            );

            $managerDefinition->addMethodCall(
                'addComponentType',
                array(new \Symfony\Component\DependencyInjection\Reference($id))
            );

        }
    }

}

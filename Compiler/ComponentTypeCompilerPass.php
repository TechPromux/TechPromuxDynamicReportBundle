<?php

namespace TechPromux\Bundle\DynamicReportBundle\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ComponentTypeCompilerPass implements CompilerPassInterface {

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('techpromux_dynamic_report.manager.util_dynamic_report')) {
            return;
        }

        $managerDefinition = $container->getDefinition(
            'techpromux_dynamic_report.manager.util_dynamic_report'
        );

        $taggedServicesIds = $container->findTaggedServiceIds(
            'techpromux_dynamic_report.type.component'
        );

        foreach ($taggedServicesIds as $id => $tags) {
            //$type = $container->getDefinition($id);
            $managerDefinition->addMethodCall(
                'addComponentType', array(new \Symfony\Component\DependencyInjection\Reference($id)));

        }
    }

}

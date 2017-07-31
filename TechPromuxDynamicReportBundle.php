<?php

namespace TechPromux\DynamicReportBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use  TechPromux\DynamicReportBundle\Compiler\ComponentTypeCompilerPass;
use TechPromux\DynamicReportBundle\Compiler\TemplateTypeCompilerPass;

class TechPromuxDynamicReportBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ComponentTypeCompilerPass());
        $container->addCompilerPass(new TemplateTypeCompilerPass());
    }
}

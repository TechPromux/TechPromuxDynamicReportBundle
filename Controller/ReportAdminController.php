<?php

namespace  TechPromux\DynamicReportBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use  TechPromux\DynamicReportBundle\Entity\Component;
use  TechPromux\DynamicReportBundle\Entity\Report;
use  TechPromux\DynamicReportBundle\Manager\ReportManager;
use  TechPromux\DynamicReportBundle\Type\Component\BaseComponentType;

class ReportAdminController extends CRUDController
{

    public function executeAction(Request $request, $id)
    {
        $object = $this->admin->getObject($id);
        /* @var $object Report */

        $this->admin->checkAccess('execute', $object);

        $reportManager = $this->admin->getResourceManager();
        /* @var $reportManager ReportManager */

        $template_name = $object->getTemplateName() ?: $reportManager->getUtilDynamicReportManager()->getDefaultTemplateName();

        $template = $reportManager->getUtilDynamicReportManager()->getTemplateTypeById($template_name);

        $components_content = array();

        $javascripts = array();

        $stylesheets = array();

        foreach ($template->getContainersNames() as $container_id) {
            $components_content[$container_id] = array();
        }

        $components = $object->getComponents();

        foreach ($components as $cmpt) {
            /* @var $cmpt Component */

            $template_container = $cmpt->getTemplateContainer();

            if (isset($components_content[$template_container]) && $cmpt->getEnabled()) {

                // validar esto dentro del componente && $wgt->getQuery()->getEnabled() && $wgt->getQuery()->getMetadata()->getEnabled() && $wgt->getQuery()->getMetadata()->getConnection()->getEnabled()

                $component_type_name = $cmpt->getComponentType();

                $component_type = $reportManager->getUtilDynamicReportManager()->getComponentTypeById($component_type_name);
                /* @var $component_type BaseComponentType */

                //$template_position = $wgt->getPosition();
                $components_content[$template_container][] = array(
                    'component' => $cmpt,
                    'component_type' => $component_type
                );

                $javascripts = array_merge($javascripts, array_diff($component_type->getJavascripts(), $javascripts));
                $stylesheets = array_merge($stylesheets, array_diff($component_type->getStylesheets(), $stylesheets));
            }
        }
        //dump($template->getAbsolutePath());

        //dump($this->get('templating.')->getPaths());

        return $this->render($this->admin->getTemplate('execute'), array(
            'request' => $request,
            'template' => $template->getAbsolutePath(),
            'action' => 'execute',
            'report' => $object,
            'components' => $components_content,
            'javascripts' => $javascripts,
            'stylesheets' => $stylesheets,
            'debug_template_zone' => $request->get('debug', false)
        ));
    }

}

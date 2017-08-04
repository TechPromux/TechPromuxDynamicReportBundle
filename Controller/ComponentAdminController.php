<?php

namespace TechPromux\DynamicReportBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use TechPromux\DynamicReportBundle\Entity\Component;
use TechPromux\DynamicReportBundle\Manager\ComponentManager;
use TechPromux\DynamicReportBundle\Type\Component\BaseComponentType;

class ComponentAdminController extends CRUDController
{

    public function copyAction($id)
    {

        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        $duplicatedComponent = $this->admin->getManager()->duplicateComponent($object);

        $this->addFlash('sonata_flash_success', 'Duplicated successfully');

        return new \Symfony\Component\HttpFoundation\RedirectResponse($this->admin->generateUrl('list'));
    }

    public function renderAction(Request $request, $childId)
    {

        $component = $this->admin->getObject($childId);
        /* @var $component Component */

        $this->admin->checkAccess('render', $component);

        $component_manager = $this->admin->getResourceManager();
        /* @var $component_manager ComponentManager */

        if ($component->getEnabled()) {

            $component_type_name = $component->getComponentType();

            $component_type = $component_manager->getUtilDynamicReportManager()->getComponentTypeById($component_type_name);

            /* @var $component_type BaseComponentType */

            $response = $component_type->render($request, $component);

            return $response;
        }

        return new \Symfony\Component\HttpFoundation\Response('');
    }

    public function executeAction(Request $request, $childId)
    {
        $component = $this->admin->getObject($childId);
        /* @var $component Component */

        $this->admin->checkAccess('execute', $component);

        $component_manager = $this->admin->getResourceManager();
        /* @var $component_manager ComponentManager */

        if ($component->getEnabled()) {

            $component_type_name = $component->getComponentType();

            $component_type = $component_manager->getUtilDynamicReportManager()->getComponentTypeById($component_type_name);

            /* @var $component_type BaseComponentType */

            $response = $component_type->render($request, $component);

            return $response;
        }

        return new \Symfony\Component\HttpFoundation\Response('');
    }

    public function exportToAction(Request $request, $childId)
    {
        $component = $this->admin->getObject($childId);
        /* @var $component Component */

        $this->admin->checkAccess('exportTo', $component);

        $component_manager = $this->admin->getResourceManager();
        /* @var $component_manager ComponentManager */

        if ($component->getEnabled()) {

            $component_type_name = $component->getComponentType();

            $component_type = $component_manager->getUtilDynamicReportManager()->getComponentTypeById($component_type_name);

            /* @var $component_type BaseComponentType */

            $response = $component_type->export($request, $component);

            return $response;
        }

        return new \Symfony\Component\HttpFoundation\Response('');
    }


    public function saveasAction($childId, $format)
    {

        $component = $this->admin->getObject($childId);

        $this->admin->checkAccess('saveas', $component);

        /*
          $allowedExportFormats = (array) $this->admin->getExportablesFormats();

          if (!in_array($format, $allowedExportFormats)) {
          throw new \RuntimeException(
          sprintf(
          'Export in format `%s` is not allowed for class: `%s`. Allowed formats are: `%s`',
          $format,
          $this->admin->getClass(),
          implode(', ', $allowedExportFormats)
          )
          );
          } */

        if ($component->getEnabled()) {
            $type = $component->getType();

            $component_service = $this->admin->getManager()->getComponentForComponentType($type);
            /* @var $component_service \TechPromux\DynamicReportBundle\ComponentBlock\ComponentInterface */

            return $component_service->export($component, $format);
        }

        return new \Symfony\Component\HttpFoundation\Response('');
    }

}

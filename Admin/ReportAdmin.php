<?php

namespace TechPromux\DynamicReportBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use TechPromux\BaseBundle\Admin\Resource\BaseResourceAdmin;
use TechPromux\DynamicReportBundle\Entity\Report;
use TechPromux\DynamicReportBundle\Manager\ReportManager;

class ReportAdmin extends BaseResourceAdmin
{
    /**
     *
     * @return string
     */
    public function getResourceManagerID()
    {
        return 'techpromux_dynamic_report.manager.report';
    }

    /**
     * @return ReportManager
     */
    public function getResourceManager()
    {
        return parent::getResourceManager(); // TODO: Change the autogenerated stub
    }

    /**
     *
     * @return Report
     */
    public function getSubject()
    {
        return parent::getSubject();
    }

    /**
     *
     * @param Report $object
     * @return string
     */
    public function toString($object)
    {
        return $object->getName() ?: '';
    }

    //----------------------------------------------------------------------------------

    protected $accessMapping = array(
        'execute' => 'VIEW',
    );

    protected function configureRoutes(\Sonata\AdminBundle\Route\RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('execute', $this->getRouterIdParameter() . '/execute');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {

        parent::configureDatagridFilters($datagridMapper);

        $datagridMapper
            ->add('name')
            ->add('title')
            //->add('description')
            //->add('template')
            //->add('position')
            //->add('type')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {

        parent::configureListFields($listMapper);

        $listMapper
            ->add('name')
            ->add('title')//->add('description')
        ;


        $listMapper->add('components', null, array(
            'row_align' => 'left',
            'header_style' => 'width: 30%',
            //'associated_property' => 'selectedTableNameOrCustomQuery',
            //'route' => array('name' => 'edit'),
        ));
        $listMapper
            ->add('enabled', null, array('editable' => true,
                'row_align' => 'center',
                'header_style' => 'width: 100px',
            ));

        $listMapper->add('_action', 'actions', array(
            //'label' => 'Actions',
            'row_align' => 'right',
            'header_style' => 'width: 150px',
            'actions' => array(
                //'show' => array(),
                'edit' => array(),
                'techpromux_dynamic_report.admin.report|techpromux_dynamic_report.admin.component.list' => array(
                    'template' => 'TechPromuxDynamicReportBundle:Admin:Component/list__action_report_component.html.twig'
                ),
                'execute' => array(
                    'template' => 'TechPromuxBaseBundle:Admin:CRUD/list__action_execute.html.twig'
                ),
                'delete' => array(),
            )
        ));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        parent::configureFormFields($formMapper);

        $formMapper
            ->with('General Information', array("class" => "col-md-8"))
            ->add('name')
            ->add('title')
            ->add('description')
            ->end()
            ->with('Estructure Design', array("class" => "col-md-4"))
            ->add('templateName', 'choice', array(
                'required' => true, 'multiple' => false, 'expanded' => false,
                'choices' => $this->getResourceManager()->getUtilDynamicReportManager()->getTemplateTypesChoices()
            ))
            ->add('enabled')
            ->end();
    }

    public function getTemplate($name)
    {
        $object = $this->getSubject();

        if ($object != null && $object->getId() != null) {
            switch ($name) {
                case 'execute':
                    return "TechPromuxDynamicReportBundle:Admin:Report/execute.html.twig";
            }
        }
        return parent::getTemplate($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureSideMenu(\Knp\Menu\ItemInterface $menu, $action, \Sonata\AdminBundle\Admin\AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit', 'execute'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        if ($admin->getRequest()->get('id') && !$admin->getRequest()->get('childId') && in_array($action, array('edit'))) {
            $menu->addChild('link_action_report_component_list', array('uri' => $admin->generateUrl('techpromux_dynamic_report.admin.report|techpromux_dynamic_report.admin.component.list', array('id' => $id))));
            $menu->addChild('link_action_execute', array('uri' => $admin->generateUrl('execute', array('id' => $id))));
        }

        if ($admin->getRequest()->get('id') && $admin->getRequest()->get('childId') && in_array($action, array('edit'))) {
            $menu->addChild('link_action_report_component_list', array('uri' => $this->getChild('techpromux_dynamic_report.admin.component')->generateUrl('list', array())));
        }

        if ($admin->getRequest()->get('childId') || ($admin->getRequest()->get('id') && in_array($action, array('list')))
        ) {
            $menu->addChild('link_action_report_edit', array('uri' => $admin->generateUrl('edit', array('id' => $id))));
            $menu->addChild('link_action_execute', array('uri' => $admin->generateUrl('execute', array('id' => $id))));
        }

    }

    public function validate2(\Sonata\CoreBundle\Validator\ErrorElement $errorElement, $object)
    {

        parent::validate($errorElement, $object);

        $errorElement
            ->end()
            ->with('title')
            ->assertNotBlank()
            ->assertLength(array('min' => 3))
            ->end()
            ->with('description')
            ->assertNotBlank()
            ->assertLength(array('min' => 5))
            ->end()
            ->with('template')
            ->assertNotBlank()
            ->end();
    }

}

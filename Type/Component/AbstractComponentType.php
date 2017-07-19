<?php

namespace TechPromux\Bundle\DynamicReportBundle\Type\Component;

use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TechPromux\Bundle\DynamicQueryBundle\Type\ConditionalOperator\BaseConditionalOperatorType;
use TechPromux\Bundle\DynamicReportBundle\Entity\Component;
use TechPromux\Bundle\DynamicReportBundle\Manager\ComponentManager;
use TechPromux\Bundle\DynamicReportBundle\Manager\UtilDynamicReportManager;

abstract class AbstractComponentType implements BaseComponentType
{
    //------------------------------------------------------------------------------------------

    /**
     * @var UtilDynamicReportManager
     */
    protected $util_dynamic_report_manager;

    /**
     * @return UtilDynamicReportManager
     */
    public function getUtilDynamicReportManager()
    {
        return $this->util_dynamic_report_manager;
    }

    /**
     * @param UtilDynamicReportManager $util_dynamic_report_manager
     * @return ComponentManager
     */
    public function setUtilDynamicReportManager($util_dynamic_report_manager)
    {
        $this->util_dynamic_report_manager = $util_dynamic_report_manager;
        return $this;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getServiceContainer()
    {
        return $this->getUtilDynamicReportManager()->getServiceContainer();
    }

    /**
     * @return object|\Symfony\Bundle\TwigBundle\TwigEngine
     */
    public function getTemplating()
    {
        return $this->getServiceContainer()->get('templating');
    }

    //-------------------------------------------------------------------

    /**
     * @return string
     */
    public function getBundleName()
    {
        return $this->getUtilDynamicReportManager()->getBundleName();
    }

    //--------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getBaseTemplateForEditForm()
    {
        return $this->getBundleName() . 'Type:Component/edit.html.twig';
    }

    /**
     * @return string
     */
    public function getTemplateForEditForm()
    {
        return $this->getBaseTemplateForEditForm();
    }

    /**
     * @return array
     */
    /**
     * @return array
     */
    public function getExportablesFormats()
    {
        return array();
    }

    /**
     * @return array
     */
    public function getExportablesFormatsIconsClasses(){
        return array();
    }

    /**
     * @return bool
     */
    public function getDataModelDatasetResultPaginated(){
        return false;
    }

    /**
     * @param Component $component
     * @param array $options
     * @return array
     */
    public function getCustomSettingsKeysForEditForm(Component $component, $options = array())
    {

        $keys = $this->createCustomSettingsKeysForEditForm($component, $options);

        return $keys;
    }

    /**
     * @param Component $component
     * @param array $options
     * @return array
     */
    public abstract function createCustomSettingsKeysForEditForm(Component $component, $options = array());


    //----------------------------------------------------------------------------------------------

    /**
     * @param ErrorElement $errorElement
     * @param Component $component
     *
     * @return ErrorElement
     */
    public function validateComponent(ErrorElement $errorElement, Component $component)
    {
        return $errorElement;
    }

    /**
     * @return Component
     *
     * @param Component $component
     */
    public function prePersist(Component $component)
    {
        return $component;
    }

    /**
     * @return Component
     *
     * @param Component $component
     */
    public function postPersist(Component $component)
    {
        return $component;
    }

    /**
     * @return Component
     *
     * @param Component $component
     */
    public function preUpdate(Component $component)
    {
        return $component;
    }

    /**
     * @return Component
     *
     * @param Component $component
     */
    public function postUpdate(Component $component)
    {
        return $component;
    }

    /**
     * @return Component
     *
     * @param Component $component
     */
    public function preRemove(Component $component)
    {
        return $component;
    }

    /**
     * @return Component
     *
     * @param Component $component
     */
    public function postRemove(Component $component)
    {
        return $component;
    }

    //-----------------------------------------------------------------------------------------------------------------

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $parameters
     *
     * @return Response
     */
    public function render(Request $request = null, Component $component, array $parameters = array())
    {
        $all = $this->getMergedDataParametersAndSettings($request, $component, $parameters);

        return $this->createComponentContent($request, $component, $all);
    }

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $parameters
     * @return mixed
     */
    public function execute(Request $request = null, Component $component, array $parameters = array())
    {
        $data = $this->getMergedDataParametersAndSettings($request, $component, $parameters);

        return $data;
    }

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $parameters
     *
     * @return mixed
     */
    public function export(Request $request = null, Component $component, array $parameters = array())
    {
        $all = $this->getMergedDataParametersAndSettings($request, $component, $parameters);

        // exportContent
        return $all;
    }

    //-----------------------------------------------------------------------------------

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $parameters
     *
     * @return string|Response
     */
    protected function createComponentContent(Request $request = null, Component $component, array $parameters = array())
    {
        if ($request->isXmlHttpRequest())
            return $this->getTemplating()->renderResponse($this->getTemplateForRenderComponent(), $parameters);
        else
            return $this->getTemplating()->render($this->getTemplateForRenderComponent(), $parameters);
    }

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $parameters
     *
     * @return string|Response
     */
    protected abstract function createComponentData(Request $request = null, Component $component, array $parameters = array());

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $parameters
     *
     * @return array
     */
    protected function getMergedDataParametersAndSettings(Request $request = null, Component $component, array $parameters = array())
    {

        $data = $this->createComponentData($request, $component, $parameters);

        $filters_form = $this->createFiltersForm($request, $component, $parameters);

        $filters_form->handleRequest($request); // or handleRequest?

        //if (!$filters_form->isValid()) { // if ($request->isXmlHttpRequest())
        //    throw new \Exception("ERROR!!!. Filter data isn´t valid");
        //}

        $all = array(
            'component' => $component,
            'component_type' => $this,
            'parameters' => $parameters,
            'settings' => array_merge(is_array($component->getDataOptions()) ? $component->getDataOptions() : array(),
                is_array($component->getComponentOptions()) ? $component->getComponentOptions() : array()),
            'data' => $data,
            'filters_form' => $filters_form->createView(),
            'extras' => array(),
        );

        return $all;
    }

    //----------------------------------------------------------------------------------------

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $parameters
     *
     * @return \Symfony\Component\Form\Form
     */
    public function createFiltersForm(Request $request = null, Component $component, array $parameters = array())
    {
        $filters_form_builder = $this->getUtilDynamicReportManager()->createNamedFormBuilder(
            'filters_form_' . strtolower(str_replace('-', '_', $component->getId())),
            array(),
            array(
                'csrf_protection' => false,
                'validation_groups' => array('filtering'),
            )
        );

        $filters_form_builder = $this->configureFiltersFormBuilder($request, $filters_form_builder, $component, $parameters);

        return $filters_form_builder->getForm();

    }

    /**
     * @param Request|null $request
     * @param FormBuilderInterface $filters_form_builder
     * @param Component $component
     * @param array $parameters
     * @return \Symfony\Component\Form\FormInterface
     */
    public abstract function configureFiltersFormBuilder(Request $request = null, $filters_form_builder, Component $component, array $parameters = array());

    //----------------------------------------------------------------------------------------

    /**
     * @return string
     */
    public abstract function getTemplateForRenderComponent();

    /**
     * @return string
     */
    public function getIconClassName()
    {
        return 'fa-cubes';
    }


    /**
     *
     * @return array
     */
    public function getJavascripts()
    {

        return array_merge($this->getComponentJavascripts(), array(
            'bundles/sonatacore/vendor/moment/min/moment-with-locales.min.js',
            'bundles/sonatacore/vendor/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
        ));
    }

    /**
     *
     * @return array
     */
    public abstract function getComponentJavascripts();

    /**
     *
     * @return array
     */
    public function getStylesheets()
    {
        return array_merge($this->getComponentStylesheets(), array(
            'bundles/sonatacore/vendor/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
        ));
    }

    /**
     *
     * @return array
     */
    public abstract function getComponentStylesheets();

    /**
     * @return string
     */
    public function getRenderActionPathName()
    {
        return 'admin_techpromux_dynamicreport_report_component_render';
    }

    /**
     * @return string
     */
    public function getExecuteActionPathName()
    {
        return 'admin_techpromux_dynamicreport_report_component_execute';
    }

    /**
     * @return string
     */
    public function getExportActionPathName()
    {
        return 'admin_techpromux_dynamicreport_report_component_export';
    }


}

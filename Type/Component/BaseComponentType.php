<?php

namespace TechPromux\DynamicReportBundle\Type\Component;

use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TechPromux\DynamicReportBundle\Entity\Component;

interface BaseComponentType
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getGroupName();

    //-------------------------------------------------------------------------------------------

    /**
     * @return array
     */
    public function getDefaultCustomSettings();

    /**
     * @return string
     */
    public function getTemplateForEditForm();

    /**
     * @return array
     */
    public function getExportablesFormats();

    /**
     * @return array
     */
    public function getExportablesFormatsIconsClasses();

    /**
     * @return boolean
     */
    public function getHasDataModelDataset();

    /**
     * @return array
     * @param Component $component
     */
    public function getCustomSettingsKeysForEditForm(Component $component);

    //-----------------------------------------------------------------------------------------------------------------

    /**
     * @param ErrorElement $errorElement
     * @param Component $component
     *
     * @return ErrorElement
     */
    public function validateComponent(ErrorElement $errorElement, Component $component);

    /**
     * @return Component
     *
     * @param Component $component
     */
    public function prePersist(Component $component);

    /**
     * @return Component
     *
     * @param Component $component
     */
    public function postPersist(Component $component);

    /**
     * @return Component
     *
     * @param Component $component
     */
    public function preUpdate(Component $component);

    /**
     * @return Component
     *
     * @param Component $component
     */
    public function postUpdate(Component $component);

    /**
     * @return Component
     *
     * @param Component $component
     */
    public function preRemove(Component $component);

    /**
     * @return Component
     *
     * @param Component $component
     */
    public function postRemove(Component $component);

    //------------------------------------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getRenderActionPathName();

    /**
     * @return string
     */
    public function getExecuteActionPathName();

    /**
     * @return string
     */
    public function getExportActionPathName();

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $parameters
     *
     * @return Response
     */
    public function render(Request $request = null, Component $component, array $parameters = array());

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $parameters
     *
     * @return Response
     */
    public function execute(Request $request = null, Component $component, array $parameters = array());

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $parameters
     *
     * @return Response
     */
    public function export(Request $request = null, Component $component, array $parameters = array());

    /**
     *
     * @return array
     */
    public function getJavascripts();

    /**
     *
     * @return array
     */
    public function getStylesheets();

    /**
     * @return string
     */
    public function getIconClassName();

}

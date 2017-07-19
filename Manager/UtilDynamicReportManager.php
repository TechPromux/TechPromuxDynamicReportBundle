<?php
/**
 * Created by PhpStorm.
 * User: franklin
 * Date: 13/07/2017
 * Time: 14:13
 */

namespace TechPromux\Bundle\DynamicReportBundle\Manager;


use TechPromux\Bundle\BaseBundle\Manager\BaseManager;
use TechPromux\Bundle\DynamicReportBundle\Type\Component\BaseComponentType;
use TechPromux\Bundle\DynamicReportBundle\Type\Component\Table\PaginatedTableComponentType;
use TechPromux\Bundle\DynamicReportBundle\Type\Template\BaseTemplateType;
use TechPromux\Bundle\DynamicReportBundle\Type\Template\DefaultTemplateType;
use TechPromux\Bundle\DynamicReportBundle\Type\Template\TwoColumnsTemplateType;

class UtilDynamicReportManager extends BaseManager
{

    /**
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'TechPromuxDynamicReportBundle';
    }

    //--------------------------------------------------------------------

    /**
     * @var ComponentManager
     */
    protected $component_manager;

    /**
     * @return ComponentManager
     */
    public function getComponentManager()
    {
        return $this->component_manager;
    }

    /**
     * @param ComponentManager $component_manager
     * @return AbstractComponentType
     */
    public function setComponentManager($component_manager)
    {
        $this->component_manager = $component_manager;
        return $this;
    }

    //------------------------------------------------------------------------

    protected $report_templates_types = array();

    /**
     * @param BaseTemplateType $report_templates_type
     * @return $this
     */
    public function addTemplateType($report_templates_type)
    {
        $this->report_templates_types[$report_templates_type->getId()] = $report_templates_type;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getRegisteredTemplatesTypes()
    {
        $this->report_templates_types = array(
            'techpromux.template.default' => new DefaultTemplateType(),
            'techpromux.template.2columns' => new TwoColumnsTemplateType(),
        );

        return $this->report_templates_types;
    }

    /**
     *
     * @return array
     */
    public function getTemplateTypesChoices()
    {
        $templates = $this->getRegisteredTemplatesTypes();

        $templates_choices = array();

        foreach ($templates as $t) {
            /* @var $t BaseTemplateType */

            $group_name = $t->getGroupName() . '_TEMPLATES';

            if (!isset($templates_choices[$group_name])) {
                $templates_choices[$group_name] = array();
            }

            $templates_choices[$group_name][$t->getId()] = $t->getId();

        }

        return $templates_choices;
    }

    /**
     * @param string $id
     * @return BaseTemplateType
     */
    public function getTemplateTypeById($id)
    {
        return $this->getRegisteredTemplatesTypes()[$id];
    }

    public function getContainersNamesByTemplate($id)
    {
        $template = $this->getTemplateTypeById($id);

        return $template->getContainersNames();
    }

    public function getDefaultTemplateName()
    {
        return 'techpromux.default';
    }

    //----------------------------------------------------------------------------

    protected $components_types = array();

    /**
     * @param BaseComponentType $components_type
     * @return $this
     */
    public function addComponentType($components_type)
    {
        $this->components_types[$components_type->getId()] = $components_type;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getRegisteredComponentTypes()
    {
        $this->components_types = array(
            'techpromux.table.paginated' => new PaginatedTableComponentType(),

        );

        foreach ($this->components_types as $c) {
            $c->setUtilDynamicReportManager($this);
        }

        return $this->components_types;
    }

    /**
     *
     * @return array
     */
    public function getComponentsTypesChoices()
    {
        $components = $this->getRegisteredComponentTypes();

        $components_choices = array();

        foreach ($components as $c) {
            /* @var $c BaseComponentType */

            $group_name = $c->getGroupName() . '_COMPONENTS';

            if (!isset($components_choices[$group_name])) {
                $components_choices[$group_name] = array();
            }

            $components_choices[$group_name][$c->getId()] = $c->getId();

        }

        return $components_choices;
    }

    /**
     *
     * @return array
     */
    public function getComponentsTypesChoicesWithoutGroups()
    {
        $components = $this->getRegisteredComponentTypes();

        $components_choices = array();

        foreach ($components as $c) {
            $components_choices[$c->getId()] = $c->getId();

        }

        return $components_choices;
    }

    /**
     * @param string $id
     * @return BaseComponentType
     */
    public function getComponentTypeById($id)
    {
        return $this->getRegisteredComponentTypes()[$id];
    }

    //-------------------------------------------------------------------------------------
    public function choicesForAdmitedQueriesForCurrentUser()
    {

        // queries, relacionadas al reporte????? y al tipo de reporte (si es no hybrid debe ser la consulta del reporte)

        $user = $this->getCurrentEntityOwner();

        $queries = $this->getDataModelManager()->getEnabledQueriesFromUser($user);

        $queries_choices = array();

        foreach ($queries as $q) {
            $queries_choices[$q->getId()] = $q->getTitle();
        }

        return $queries_choices;
    }

    public function choicesForQueryDetailsFromSelectedQuery($query_id)
    {

        $details_choices = array();

        $details = $this->getDataModelManager()->descriptionForPublicDetailsFromQuery($query_id);
        foreach ($details as $dt) {
            $details_choices[$dt['id']] = $dt['title'] . ' (' . $dt['abbreviation'] . ')';
        }

        return $details_choices;
    }

    public function choicesForNumericQueryDetailsFromSelectedQuery($query_id)
    {

        $details_choices = array();
        $details = $this->getDataModelManager()->descriptionForPublicDetailsFromQuery($query_id);
        foreach ($details as $dt) {
            if ($dt['numeric_alpha_datetime'] == 'numeric') {
                $details_choices[$dt['id']] = $dt['title'] . ' (' . $dt['abbreviation'] . ')';
            }
        }

        return $details_choices;
    }

    public function choicesForNumericAndDatetimeQueryDetailsFromSelectedQuery($query_id)
    {

        $details_choices = array();
        $details = $this->getDataModelManager()->descriptionForPublicDetailsFromQuery($query_id);
        foreach ($details as $dt) {
            if ($dt['numeric_alpha_datetime'] == 'numeric' || $dt['numeric_alpha_datetime'] == 'datetime') {
                $details_choices[$dt['id']] = $dt['title'] . ' (' . $dt['abbreviation'] . ')';
            }
        }

        return $details_choices;
    }


}
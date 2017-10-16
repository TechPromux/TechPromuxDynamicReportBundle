<?php

namespace TechPromux\DynamicReportBundle\Type\Component\Table;

use Symfony\Component\HttpFoundation\Request;
use TechPromux\DynamicReportBundle\Entity\Component;
use TechPromux\DynamicReportBundle\Type\Component\DataModel\AbstractDataModelComponentType;
use TechPromux\DynamicReportBundle\Type\Component\Response;

class CrossedTableComponentType extends AbstractDataModelComponentType
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'techpromux.table.crossed';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'table.crossed';
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return 'techpromux.tables';
    }

    //------------------------------------------------------------------

    /**
     * @return array
     */
    public function getDefaultCustomSettings()
    {
        return array(
            'basic_options' => array(
                'data_summary_function' => '',
                'data_summary_label' => '',
                'header_first_column_text_with' => '',
                'show_row_number' => true
            ),
            'limit_indicators' => array(),
        );
    }

    /**
     * @return string
     */
    public function getTemplateForEditForm()
    {
        return '@' . $this->getBundleName() . '/Type/Component/Table/Crossed/edit.html.twig';
    }

    /**
     * @return boolean
     */
    public function getHasDataModelDataset()
    {
        return true;
    }

    /**
     * @return boolean
     */
    public function getHasDataModelDatasetLabel()
    {
        return true;
    }

    /**
     * @return boolean
     */
    public function getHasDataModelDatasetSeries()
    {
        return true;
    }

    /**
     * @return boolean
     */
    public function getHasDataModelDatasetMultipleDatas()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function getDataModelDatasetResultPaginated()
    {
        return false;
    }

    /**
     * 'all', 'number', 'datetime', 'number_datetime'
     *
     * @return string
     */
    public function getSupportedDataTypeFromDataModelDetails()
    {
        return 'number_datetime';
    }


    /**
     * @param Component $component
     * @param array $options
     */
    public function createCustomSettingsKeysForEditForm(Component $component, $options = array())
    {
        //$details_choices = $options['details_choices'];

        $details_numeric_datetime_choices = $options['details_numeric_datetime_choices'];

        //-------------------------------------------------------------

        $keys = array();

        $keys[] = array('basic_options', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('data_summary_function', 'choice', array(
                    "multiple" => false, "expanded" => false, "required" => false,
                    'choices' => array(
                        'SUM' => 'SUM',
                        'AVG' => 'AVG',
                        'COUNT' => 'COUNT',
                        'MIN' => 'MIN',
                        'MAX' => 'MAX',
                    ),
                    "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                    'translation_domain' => $this->getBundleName()
                )),
                array('data_summary_label', 'text', array(
                    "required" => false,
                    "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                    'attr' => array(
                        //'placeholder' => 'px',
                        //'style' => 'width: 70px;'
                    ),
                    'translation_domain' => $this->getBundleName()
                )),
                array('header_first_column_text_with', 'text', array(
                    "required" => false,
                    "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                    'attr' => array(
                        'placeholder' => 'px',
                        //'style' => 'width: 70px;'
                    ),
                    'translation_domain' => $this->getBundleName()
                )),
                array('show_row_number', 'checkbox', array(
                    'required' => false,
                    "label_attr" => array(
                        'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'
                    ),
                    'translation_domain' => $this->getBundleName()
                )),
            )
        ));

        $keys[] = array('limit_indicators', 'sonata_type_native_collection', array(
            'entry_type' => 'sonata_type_immutable_array',
            'allow_add' => true,
            'allow_delete' => true,
            'entry_options' => array(
                'keys' => array(
                    /*
                    array('detail_id', 'choice', array(
                        //'label' => 'Detail',
                        "multiple" => false, "expanded" => false, "required" => true,
                        'choices' => $details_numeric_datetime_choices,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-3'),
                    )
                    ),
                    */
                    array('limit_type', 'choice', array(
                        'choices' => array(
                            'less_than' => 'less_than',
                            'less_or_equal' => 'less_or_equal',
                            'greater_than' => 'greater_than',
                            'greater_or_equal' => 'greater_or_equal',
                            'between' => 'between',
                            'not_between' => 'not_between',
                        ),
                        'required' => true,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
                        //'translation_domain' => $this->getBundleName()
                    )),
                    array('limit', 'text', array(
                        'required' => true,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
                        //'translation_domain' => $this->getBundleName()
                    )),
                    array('limit_color', 'text', array(
                        "required" => true,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
                        //'read_only' => true,
                        'empty_data' => 'rgb(0,0,255)',
                        'attr' => array('class' => 'color-picker', 'style' => 'width: 140px;'),
                        //'translation_domain' => $this->getBundleName()
                    )),
                    array('limit_message', 'text', array(
                        'required' => false,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-3'),
                        //'translation_domain' => $this->getBundleName()
                    )),
                )
            )
        ));

        return $keys;
    }

    //------------------------------------------------------------------------

    /**
     * return string
     */
    public function getTemplateForRenderComponent()
    {
        return '@' . $this->getBundleName() . '/Type/Component/Table/Crossed/render.html.twig';
    }

    /**
     *
     * @return array
     */
    public function getComponentJavascripts()
    {
        return array();
    }

    /**
     *
     * @return array
     */
    public function getComponentStylesheets()
    {
        return array();
    }


    public function getIconClassName()
    {
        return 'fa-table';
    }

    //----------------------------------------------------------

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $data
     * @param array $parameters
     * @param bool $full_exportable_data
     * @return array
     */
    protected function getMergedDataParametersAndSettings(Request $request = null, Component $component, array $parameters = array(), $full_exportable_data = false)
    {
        $all = parent::getMergedDataParametersAndSettings($request, $component, $parameters, $full_exportable_data);

        $settings = $component->getComponentOptions();

        $limit_indicators_options = isset($settings['limit_indicators']) ? $settings['limit_indicators'] : array();

        $all['settings']['_limit_indicators'] = $limit_indicators_options;

        $all['settings']['_data_summary_function'] = $all['settings']['basic_options']['data_summary_function'];
        $all['settings']['_data_summary_label'] = $all['settings']['basic_options']['data_summary_label'];

        return $all;
    }

    public function createExportableData(Request $request = null, Component $component, array $parameters = array())
    {
        $exportableData = parent::createExportableData($request, $component, $parameters); // TODO: Change the autogenerated stub

        $options = $component->getComponentOptions();

        $exportableData['show_row_number'] = $options['basic_options']['show_row_number'];

        return $exportableData;
    }
}

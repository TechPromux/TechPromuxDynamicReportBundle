<?php

namespace TechPromux\Bundle\DynamicReportBundle\Type\Component\Table;

use Symfony\Component\HttpFoundation\Request;
use TechPromux\Bundle\DynamicReportBundle\Entity\Component;
use TechPromux\Bundle\DynamicReportBundle\Type\Component\AbstractDataModelComponentType;
use TechPromux\Bundle\DynamicReportBundle\Type\Component\Response;

class PaginatedTableComponentType extends AbstractDataModelComponentType
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'techpromux.table.paginated';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'table.paginated';
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
            'paginator_options' => array(
                'initial_page' => 1,
                'items_per_page' => 32,
                'max_paginator_links' => 5,
                'show_row_number_in_first_column' => true,
            ),
            'limit_indicators' => array(),
        );
    }

    /**
     * @return string
     */
    public function getTemplateForEditForm()
    {
        return $this->getBundleName() . ':Type:Component/Table/Paginated/edit.html.twig';
    }

    /**
     * @return boolean
     */
    public function getHasDataModelDataset()
    {
        return true;
    }

    /**
     * 'multiple', 'multiple_without_label', 'crossed' or 'single'
     *
     * @return string
     */
    public function getDataModelDatasetType()
    {
        return 'multiple_without_label';
    }

    /**
     * @return bool
     */
    public function getDataModelDatasetResultPaginated(){
        return true;
    }

    /**
     * 'all', 'number', 'datetime', 'number_datetime'
     *
     * @return string
     */
    public function getDataModelDatasetWithDataDetailsType()
    {
        return 'all';
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

        $keys[] = array('paginator_options', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('initial_page', 'number', array('label' => 'Initial Page',
                    "label_attr" => array(
                        //'class' => 'pull-left',
                        'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'
                    ),
                )),
                array('items_per_page', 'number', array('label' => 'Items Per Page',
                    "label_attr" => array(
                        // 'class' => 'pull-left',
                        'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'
                    ),
                )),
                array('max_paginator_links', 'number', array('label' => 'Max Paginator Links',
                    "label_attr" => array(
                        //'class' => 'pull-left',
                        'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-3'
                    ),
                )),
                array('show_row_number_in_first_column', 'checkbox', array(
                    'required' => false,
                    "label_attr" => array(
                        //'class' => 'pull-left',
                        //'style' => 'margin-right:5px;',
                        'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'
                    ),
                )),
            )
        ));

        $keys[] = array('limit_indicators', 'sonata_type_native_collection', array(
            'entry_type' => 'sonata_type_immutable_array',
            'allow_add' => true,
            'allow_delete' => true,
            'entry_options' => array(
                'keys' => array(
                    array('detail_id', 'choice', array(
                        //'label' => 'Detail',
                        "multiple" => false, "expanded" => false, "required" => true,
                        'choices' => $details_numeric_datetime_choices,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-3'),
                    )
                    ),
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
                    )
                    ),
                    array('limit', 'text', array(
                        'required' => true,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
                    )
                    ),
                    array('limit_color', 'text', array(
                        "required" => true,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
                        //'read_only' => true,
                        'empty_data' => 'rgb(0,0,255)',
                        'attr' => array('class' => 'color-picker', 'style' => 'width: 140px;'),
                    )),
                    array('limit_message', 'text', array(
                        'required' => false,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-3'),
                    )
                    ),
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
        return $this->getBundleName() . ':Type:Component/Table/Paginated/render.html.twig';
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
        return 'fa-list-alt';
    }

    //----------------------------------------------------------

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $data
     * @param array $parameters
     * @return array
     */
    protected function getMergedDataParametersAndSettings(Request $request = null, Component $component, array $data = array(), array $parameters = array())
    {
        $all = parent::getMergedDataParametersAndSettings($request, $component, $data, $parameters);

        $settings = $component->getComponentOptions();

        $limit_indicators = array();

        $limit_indicators_options = isset($settings['limit_indicators']) ? $settings['limit_indicators'] : array();

        foreach ($limit_indicators_options as $lio) {
            if ($lio['limit_type'] == 'between' || $lio['limit_type'] == 'not_between') {
                $values = explode(',', $lio['limit'] . ',');
                $min = trim($values[0]);
                $max = trim($values[1]);
                $lio['limit'] = array($min, $max);
            }
            if (!isset($limit_indicators[$lio['detail_id']])) {
                $limit_indicators[$lio['detail_id']] = array();
            }
            $limit_indicators[$lio['detail_id']][] = $lio;
        }

        $all['settings']['limit_indicators'] = $limit_indicators;

        return $all;
    }


}

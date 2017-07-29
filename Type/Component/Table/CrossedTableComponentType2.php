<?php

namespace  TechPromux\DynamicReportBundle\Type\Component\Table;

use  TechPromux\DynamicReportBundle\Entity\Component;
use  TechPromux\DynamicReportBundle\Type\Component\AbstractComponentType;

class CrossedTableComponentType2 extends AbstractComponentType {

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

    //-------------------------------------------------------------------------------------


    //------------------------------------------------------------------

    /**
     * @return array
     */
    public function getDefaultCustomSettings()
    {
        return array(
            'limit_indicator' => array(
                'limit_type'=>null,
                'limit'=>null,
                'limit_color'=>null,
                'limit_message'=>null,
            ),
        );
    }

    /**
     * @return string
     */
    public function getTemplateForEditForm()
    {
        return $this->getBundleName() . ':Type:Component/Table/Crossed/edit.html.twig';
    }

    /**
     * @param Component $component
     * @param array $options
     */
    public function createCustomSettingsKeysForEditForm(Component $component, $options = array())
    {
        //$details_choices = $options['details_choices'];

        $details = $options['details'];

        $details_numeric_datetime_choices = array();

        foreach ($details as $dt) {
            if ($dt['classification']=='number' || $dt['classification']=='datetime')
                $details_numeric_datetime_choices[$dt['title'] . ' (' . $dt['abbreviation'] . ')'] = $dt['id'];
        }


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
                array('show_row_number', 'checkbox', array(
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
                        'choices'=> array(
                            'less_than'=>'less_than',
                            'less_or_equal'=>'less_or_equal',
                            'greater_than'=>'greater_than',
                            'greater_or_equal'=>'greater_or_equal',
                            'between'=>'between',
                            'not_between'=>'not_between',

                        ),
                        'required' => false,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
                    )
                    ),
                    array('limit', 'text', array(
                        'required' => false,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
                    )
                    ),
                    array('limit_color', 'text', array(
                        "required" => false,
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
        return $this->getBundleName() . 'Type:Component/Table/Crossed/render.html.twig';
    }

    /**
     *
     * @return array
     */
    public function getComponentJavascripts()
    {
        // TODO: Implement getComponentJavascripts() method.
    }

    /**
     *
     * @return array
     */
    public function getComponentStylesheets()
    {
        // TODO: Implement getComponentStylesheets() method.
    }


    public function getIconClassName()
    {
        return 'fa-list-alt';
    }

    //----------------------------------------------------------


    public function createExportableData(\TechPromux\DynamicReportBundle\Entity\Component $component, \Doctrine\DBAL\Query\QueryBuilder $queryBuilder) {

        // OBTAIN SETTINGS

        $settings = $component->getSettings();

        $result = $queryBuilder->execute()->fetchAll();

        $datamodel_details_descriptions = $this->getComponentManager()->getReportManager()->getDataModelManager()->descriptionForPublicDetailsFromQuery($component->getQuery()->getId());

        $data = array(
            'title' => $component->getTitle(),
            'labels' => array(),
            'data' => array()
        );

        $detail_for_label_options = $settings['detail_for_label'];
        $detail_for_series_options = $settings['detail_for_crossed_datasets_series'];
        $detail_for_datas_options = $settings['detail_for_crossed_datasets_datas'];

        $chart_labels = array();
        $chart_series = array();
        $chart_datas = array();

        foreach ($result as $row) {

            $col_label = $row[$datamodel_details_descriptions[$detail_for_label_options['detail_id']]['sql_alias']];
            $l_format = $datamodel_details_descriptions[$detail_for_label_options['detail_id']]['format'];
            $l_prefix = $detail_for_label_options['show_prefix'] ? $datamodel_details_descriptions[$detail_for_label_options['detail_id']]['prefix'] : '';
            $l_suffix = $detail_for_label_options['show_suffix'] ? $datamodel_details_descriptions[$detail_for_label_options['detail_id']]['suffix'] : '';
            $col_label_formatted = $this->getComponentManager()->getReportManager()->formatValue($col_label, $l_format, $l_prefix, $l_suffix);
            $chart_labels[$col_label_formatted] = $col_label_formatted;

            $col_series = $row[$datamodel_details_descriptions[$detail_for_series_options['detail_id']]['sql_alias']];
            $s_format = $datamodel_details_descriptions[$detail_for_series_options['detail_id']]['format'];
            $s_prefix = $detail_for_series_options['show_prefix'] ? $datamodel_details_descriptions[$detail_for_series_options['detail_id']]['prefix'] : '';
            $s_suffix = $detail_for_series_options['show_suffix'] ? $datamodel_details_descriptions[$detail_for_series_options['detail_id']]['suffix'] : '';
            $col_series_formatted = $this->getComponentManager()->getReportManager()->formatValue($col_series, $s_format, $s_prefix, $s_suffix);
            $chart_series[$col_series_formatted] = $col_series_formatted;

            if (!isset($chart_datas[$col_series_formatted])) {
                $chart_datas[$col_series_formatted] = array();
            }

            $col_datas = $row[$datamodel_details_descriptions[$detail_for_datas_options['detail_id']]['sql_alias']];
            $chart_datas[$col_series_formatted][$col_label_formatted] = $col_datas;
        }

        foreach ($chart_labels as $label) {
            $data['labels'][] = array(
                'label' => $label,
                'type' => $datamodel_details_descriptions[$detail_for_datas_options['detail_id']]['type'],
                'prefix' => $detail_for_datas_options['show_prefix'] ? $datamodel_details_descriptions[$detail_for_datas_options['detail_id']]['prefix'] : '',
                'suffix' => $detail_for_datas_options['show_prefix'] ? $datamodel_details_descriptions[$detail_for_datas_options['detail_id']]['suffix'] : '',
            );
        }

        foreach (array_keys($chart_datas) as $serie) {
            $data['data'][$serie] = array();
            foreach ($chart_labels as $label) {
                $data_value = isset($chart_datas[$serie][$label]) ? $chart_datas[$serie][$label] : 0;
                //$d_format = $datamodel_details_descriptions[$detail_for_datas_options['detail_id']]['format'];
                //$d_prefix = $detail_for_datas_options['show_prefix'] ? $datamodel_details_descriptions[$detail_for_datas_options['detail_id']]['prefix'] : '';
                //$d_suffix = $detail_for_datas_options['show_prefix'] ? $datamodel_details_descriptions[$detail_for_datas_options['detail_id']]['suffix'] : '';
                //$data_value_formatted = $this->getComponentManager()->getReportManager()->formatValue($data_value, $d_format, '', '', true);
                $data['data'][$serie][] = $data_value;
            }
        }

        return $data;
    }

    public function detailsDescriptionsToFilterBy(\TechPromux\DynamicReportBundle\Entity\Component $component) {

        $settings = $component->getSettings();

        $details_for_filter = array();

        $details_ids = array();
        if ($settings['detail_for_label']['show_filter'] == true) {
            $details_ids[] = $settings['detail_for_label']['detail_id'];
        }
        if ($settings['detail_for_crossed_datasets_series']['show_filter'] == true) {
            $details_ids[] = $settings['detail_for_crossed_datasets_series']['detail_id'];
        }
        if ($settings['detail_for_crossed_datasets_datas']['show_filter'] == true) {
            $details_ids[] = $settings['detail_for_crossed_datasets_datas']['detail_id'];
        }

        foreach ($details_ids as $detail_id) {
            $query_detail = $this->getComponentManager()->getReportManager()->getDataModelManager()->findQueryDetailById($detail_id);
            if ($query_detail && $query_detail->getIsPublic()) {
                $details_for_filter[$detail_id] = array(
                    'id' => $detail_id,
                    'type' => $this->getComponentManager()->getReportManager()->getDataModelManager()->typeForQueryDetail($query_detail),
                    'title' => $query_detail->getTitle()
                );
            }
        }

        return $details_for_filter;
    }

    public function detailsForOrderOptionsInCrossedTable(\TechPromux\DynamicReportBundle\Entity\Component $component) {
        $settings = $component->getSettings();

        $order_by_options = array();

        $datamodel_details_descriptions = $this->getComponentManager()->getReportManager()->getDataModelManager()->descriptionForPublicDetailsFromQuery($component->getQuery()->getId());

        $order_by_options[] = array(
            'id' => $settings['detail_for_label']['detail_id'],
            'title' => $datamodel_details_descriptions[$settings['detail_for_label']['detail_id']]['title'],
            'abbreviation' => $datamodel_details_descriptions[$settings['detail_for_label']['detail_id']]['abbreviation'],
            'sort_icon' => $datamodel_details_descriptions[$settings['detail_for_label']['detail_id']]['sort_icon'],
        );

        $order_by_options[] = array(
            'id' => $settings['detail_for_crossed_datasets_series']['detail_id'],
            'title' => $datamodel_details_descriptions[$settings['detail_for_crossed_datasets_series']['detail_id']]['title'],
            'abbreviation' => $datamodel_details_descriptions[$settings['detail_for_crossed_datasets_series']['detail_id']]['abbreviation'],
            'sort_icon' => $datamodel_details_descriptions[$settings['detail_for_crossed_datasets_series']['detail_id']]['sort_icon'],
        );

        $order_by_options[] = array(
            'id' => $settings['detail_for_crossed_datasets_datas']['detail_id'],
            'title' => $datamodel_details_descriptions[$settings['detail_for_crossed_datasets_datas']['detail_id']]['title'],
            'abbreviation' => $datamodel_details_descriptions[$settings['detail_for_crossed_datasets_datas']['detail_id']]['abbreviation'],
            'sort_icon' => $datamodel_details_descriptions[$settings['detail_for_crossed_datasets_datas']['detail_id']]['sort_icon'],
        );

        return $order_by_options;
    }

    public function preUpdate(\TechPromux\DynamicReportBundle\Entity\Component $component) {

        parent::preUpdate($component);

        return $component;
    }

    public function renderDefaultResponse(\TechPromux\DynamicReportBundle\Entity\Component $component, \Doctrine\DBAL\Query\QueryBuilder $queryBuilder) {

        $result = $this->createExportableData($component, $queryBuilder);

        return $this->createDefaultResponse($component, array(
            'result' => $result,
            'order_by_options' => $this->detailsForOrderOptionsInCrossedTable($component),
        ));
    }
}

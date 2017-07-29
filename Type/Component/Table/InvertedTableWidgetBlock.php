<?php

namespace  TechPromux\DynamicReportBundle\Type\Component\Chart;

class InvertedTableComponentBlock extends \TechPromux\DynamicReportBundle\Type\Component\AbstractComponentBlock {

    public function __construct($type) {
        parent::__construct($type);
    }

    public function getName() {
        return 'Inverted Table';
    }

    public function getGroupType() {
        return 'Tables';
    }

    /**
     *
     * @return array
     */
    public function getComponentJavascripts() {
        return array(
            'vendor/chartjs/Chart.js/Chart.min.js',
        );
    }

    /**
     * @return array
     */
    public function getExportablesFormats() {
        return array_merge(parent::getExportablesFormats(), array('png' => 'png'));
    }

    public function getHasSeriesData(\TechPromux\DynamicReportBundle\Entity\Component $component) {
        return true;
    }

    public function getDefaultColorsForSeries() {

        // add others colors
        /* TODO
         * rojo, rosado
         * verde oscuro, verde claro,
         * azul oscuro, azul claro
         * anaranjado, violeta
         * gris, negro
         */
        return array(
            "255,128,0",
            "0,128,0",
            "0,128,128",
            "255,255,0",
            "128,0,128",
            "151,187,205",
            "220,220,220",
            "64,0,0",
            "140,166,120",
            "220,0,128",
            "65,65,65",
        );
    }

    /**
     * 
     * @return array
     */
    public function getDefaultCustomSettings() {

        $default_settings = array();

        $default_settings['chart_options'] = array(
            'chart_type' => null, // 'cross_datasets'
            'width' => null,
            'height' => null,
        );

        $default_settings['detail_for_label'] = array(
            'detail_id' => null,
            'show_prefix' => true,
            'show_suffix' => true,
            'show_filter' => true,
        );

        $default_settings['details_for_multiple_datasets'] = array();

        $default_settings['detail_for_crossed_datasets_series'] = array(
            'detail_id' => null,
            'show_prefix' => true,
            'show_suffix' => true,
            'show_filter' => true,
        );

        $default_settings['detail_for_crossed_datasets_datas'] = array(
            'detail_id' => null,
            'show_prefix' => true,
            'show_suffix' => true,
            'show_filter' => true,
        );

        return $default_settings;
    }

    public function createEditFormKeysSettings(\TechPromux\DynamicReportBundle\Entity\Component $component) {

        $keys = array();

        $keys['chart_options'] = array('chart_options', 'sonata_type_immutable_array', array(
                'label' => $this->trans('Basic Chart Options'),
                'keys' => array(
                    array('chart_type', 'hidden', array(
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'hide'),
                        )),
                    array('width', 'number', array(
                            'label' => 'Width',
                            "required" => false,
                            "label_attr" => array(
                                //'class' => 'pull-left',
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1'
                            ),
                            'attr' => array('placeholder' => 'px', 'style' => 'width: 70px;'),
                        )),
                    array('height', 'number', array(
                            'label' => 'Height',
                            "required" => false,
                            "label_attr" => array(
                                // 'class' => 'pull-left',
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1'
                            ),
                            'attr' => array('placeholder' => 'px', 'style' => 'width: 70px;'),
                        )),
                )
        ));

        $keys['detail_for_label'] = array('detail_for_label', 'sonata_type_immutable_array', array(
                'label' => $this->trans('Details for Horizontal Labels and Datasets'),
                'keys' => array(
                    array('detail_id', 'choice', array(
                            'label' => 'Detail for Labels',
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => $this->getComponentManager()->getReportManager()->choicesForQueryDetailsFromSelectedQuery($component->getQuery()->getId()),
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'),
                        )
                    ),
                    array('show_prefix', 'checkbox', array(
                            'label' => 'Prefix',
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                    array('show_suffix', 'checkbox', array(
                            'label' => 'Suffix',
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                    array('show_filter', 'checkbox', array(
                            'label' => 'Filter',
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                )
        ));

        $keys['detail_for_crossed_datasets_series'] = array('detail_for_crossed_datasets_series', 'sonata_type_immutable_array', array(
                'label' => false,
                'keys' => array(
                    array('detail_id', 'choice', array(
                            'label' => 'Detail for Series',
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => $this->getComponentManager()->getReportManager()->choicesForQueryDetailsFromSelectedQuery($component->getQuery()->getId()),
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-4'),
                        )
                    ),
                    array('show_prefix', 'checkbox', array(
                            'label' => 'Prefix',
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                    array('show_suffix', 'checkbox', array(
                            'label' => 'Suffix',
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                    array('show_filter', 'checkbox', array(
                            'label' => 'Filter',
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                )
        ));

        $keys['detail_for_crossed_datasets_datas'] = array('detail_for_crossed_datasets_datas', 'sonata_type_immutable_array', array(
                'label' => false,
                'keys' => array(
                    array('detail_id', 'choice', array(
                            'label' => 'Detail for Data',
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => $this->getComponentManager()->getReportManager()->choicesForNumericQueryDetailsFromSelectedQuery($component->getQuery()->getId()),
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-4'),
                        )
                    ),
                    array('show_prefix', 'checkbox', array(
                            'label' => 'Prefix',
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                    array('show_suffix', 'checkbox', array(
                            'label' => 'Suffix',
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                    array('show_filter', 'checkbox', array(
                            'label' => 'Filter',
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                )
        ));

        $keys['details_for_multiple_datasets'] = array('details_for_multiple_datasets', 'sonata_type_native_collection', array(
                'label' => $this->trans('Details for Multiple Datasets'),
                'type' => 'sonata_type_immutable_array',
                'allow_add' => true,
                'allow_delete' => true,
                'options' => array(
                    'keys' => array(
                        array('detail_id', 'choice', array(
                                'label' => 'Detail',
                                "multiple" => false, "expanded" => false, "required" => true,
                                'choices' => $this->getComponentManager()->getReportManager()->choicesForNumericQueryDetailsFromSelectedQuery($component->getQuery()->getId()),
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-5'),
                            )
                        ),
                        array('serie_color', 'text', array(
                                'label' => 'Serie Color',
                                "required" => false,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-3'),
                                'read_only' => true,
                                'empty_data' => 'rgb(0,0,0)',
                                'attr' => array('class' => 'color-picker'),
                            )),
                        array('show_prefix', 'checkbox', array(
                                'label' => 'Prefix',
                                'required' => false,
                                "label_attr" => array(
                                    'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1',
                                    'style' => 'width: 170px;max-width: 200%;'
                                ),
                            )),
                        array('show_suffix', 'checkbox', array(
                                'label' => 'Suffix',
                                'required' => false,
                                "label_attr" => array(
                                    'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1',
                                    'style' => 'width: 170px;max-width: 200%;'
                                ),
                            )),
                        array('show_filter', 'checkbox', array(
                                'label' => 'Filter',
                                'required' => false,
                                "label_attr" => array(
                                    'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1',
                                    'style' => 'width: 170px;max-width: 200%;'
                                ),
                            )),
                    )
                )
        ));

        return $keys;
    }

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

        $series_datasets_type = $settings['chart_options']['series_datasets_type'];

        if ($series_datasets_type == 'crossed_datasets') {

            $detail_for_label_options = $settings['detail_for_label'];
            $detail_to_series_options = $settings['detail_for_crossed_datasets_series'];
            $detail_to_datas_options = $settings['detail_for_crossed_datasets_datas'];

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

                $col_series = $row[$datamodel_details_descriptions[$detail_to_series_options['detail_id']]['sql_alias']];
                $s_format = $datamodel_details_descriptions[$detail_to_series_options['detail_id']]['format'];
                $s_prefix = $detail_to_series_options['show_prefix'] ? $datamodel_details_descriptions[$detail_to_series_options['detail_id']]['prefix'] : '';
                $s_suffix = $detail_to_series_options['show_suffix'] ? $datamodel_details_descriptions[$detail_to_series_options['detail_id']]['suffix'] : '';
                $col_series_formatted = $this->getComponentManager()->getReportManager()->formatValue($col_series, $s_format, $s_prefix, $s_suffix);
                $chart_series[$col_series_formatted] = $col_series_formatted;

                if (!isset($chart_datas[$col_series_formatted])) {
                    $chart_datas[$col_series_formatted] = array();
                }

                $col_datas = $row[$datamodel_details_descriptions[$detail_to_datas_options['detail_id']]['sql_alias']];
                $chart_datas[$col_series_formatted][$col_label_formatted] = $col_datas;
            }

            foreach ($chart_labels as $label) {
                $data['labels'][] = array(
                    'label' => $label,
                    'type' => $datamodel_details_descriptions[$detail_to_datas_options['detail_id']]['type'],
                    'prefix' => $detail_to_datas_options['show_prefix'] ? $datamodel_details_descriptions[$detail_to_datas_options['detail_id']]['prefix'] : '',
                    'suffix' => $detail_to_datas_options['show_prefix'] ? $datamodel_details_descriptions[$detail_to_datas_options['detail_id']]['suffix'] : '',
                );
            }

            foreach (array_keys($chart_datas) as $serie) {
                $data['data'][$serie] = array();
                foreach ($chart_labels as $label) {
                    $data_value = isset($chart_datas[$serie][$label]) ? $chart_datas[$serie][$label] : 0;
                    $d_format = $datamodel_details_descriptions[$detail_to_datas_options['detail_id']]['format'];
                    //$d_prefix = $detail_to_datas_options['show_prefix'] ? $datamodel_details_descriptions[$detail_to_datas_options['detail_id']]['prefix'] : '';
                    //$d_suffix = $detail_to_datas_options['show_prefix'] ? $datamodel_details_descriptions[$detail_to_datas_options['detail_id']]['suffix'] : '';
                    $data_value_formatted = $this->getComponentManager()->getReportManager()->formatValue($data_value, $d_format, '', '', true);
                    $data['data'][$serie][] = $data_value_formatted;
                }
            }
        } else if ($series_datasets_type == 'multiple_datasets') {

            $detail_for_label_options = $settings['detail_for_label'];
            $details_to_multiple_datasets_options = $settings['details_for_multiple_datasets'];

            foreach ($details_to_multiple_datasets_options as $detail_to_serie_datas_options) {
                $serie_label = $datamodel_details_descriptions[$detail_to_serie_datas_options['detail_id']]['title'];
                $data['data'][$serie_label] = array();
            }
            foreach ($result as $row) {
                $col_label = $row[$datamodel_details_descriptions[$detail_for_label_options['detail_id']]['sql_alias']];
                $l_format = $datamodel_details_descriptions[$detail_for_label_options['detail_id']]['format'];
                $l_prefix = $detail_for_label_options['show_prefix'] ? $datamodel_details_descriptions[$detail_for_label_options['detail_id']]['prefix'] : '';
                $l_suffix = $detail_for_label_options['show_suffix'] ? $datamodel_details_descriptions[$detail_for_label_options['detail_id']]['suffix'] : '';
                $col_label_formatted = $this->getComponentManager()->getReportManager()->formatValue($col_label, $l_format, $l_prefix, $l_suffix);

                $data['labels'][] = array(
                    'label' => $col_label_formatted,
                    'type' => 'float',
                    'prefix' => '',
                    'suffix' => '',
                );

                foreach ($details_to_multiple_datasets_options as $detail_to_serie_datas_options) {
                    $serie_label = $datamodel_details_descriptions[$detail_to_serie_datas_options['detail_id']]['title'];
                    $sd_value = $row[$datamodel_details_descriptions[$detail_to_serie_datas_options['detail_id']]['sql_alias']];
                    $sd_format = $datamodel_details_descriptions[$detail_to_serie_datas_options['detail_id']]['format'];
                    //$sd_prefix = $detail_to_serie_datas_options['show_prefix'] ? $datamodel_details_descriptions[$detail_to_serie_datas_options['detail_id']]['prefix'] : '';
                    //$sd_suffix = $detail_to_serie_datas_options['show_suffix'] ? $datamodel_details_descriptions[$detail_to_serie_datas_options['detail_id']]['suffix'] : '';
                    $sd_value_formatted = $this->getComponentManager()->getReportManager()->formatValue($sd_value, $sd_format, '', '', true);
                    $data['data'][$serie_label][] = $sd_value_formatted;
                }
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
        if ($settings['chart_options']['series_datasets_type'] == 'crossed_datasets') {
            if ($settings['detail_for_crossed_datasets_series']['show_filter'] == true)
                $details_ids[] = $settings['detail_for_crossed_datasets_series']['detail_id'];
            if ($settings['detail_for_crossed_datasets_datas']['show_filter'] == true)
                $details_ids[] = $settings['detail_for_crossed_datasets_datas']['detail_id'];
        } elseif ($settings['chart_options']['series_datasets_type'] == 'multiple_datasets') {
            foreach ($settings['details_for_multiple_datasets'] as $dmdst) {
                if ($dmdst['show_filter'] == true)
                    $details_ids[] = $dmdst['detail_id'];
            }
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

    public function detailsForOrderOptionsInChart(\TechPromux\DynamicReportBundle\Entity\Component $component) {
        $settings = $component->getSettings();

        $order_by_options = array();

        $datamodel_details_descriptions = $this->getComponentManager()->getReportManager()->getDataModelManager()->descriptionForPublicDetailsFromQuery($component->getQuery()->getId());

        $order_by_options[] = array(
            'id' => $settings['detail_for_label']['detail_id'],
            'title' => $datamodel_details_descriptions[$settings['detail_for_label']['detail_id']]['title'],
            'abbreviation' => $datamodel_details_descriptions[$settings['detail_for_label']['detail_id']]['abbreviation'],
            'sort_icon' => $datamodel_details_descriptions[$settings['detail_for_label']['detail_id']]['sort_icon'],
        );

        $series_datasets_type = $settings['chart_options']['series_datasets_type'];

        if ($series_datasets_type == 'crossed_datasets') {

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
        } else if ($series_datasets_type == 'multiple_datasets') {

            $details_to_multiple_datasets_options = $settings['details_for_multiple_datasets'];

            foreach ($details_to_multiple_datasets_options as $detail_to_serie_datas_options) {
                $order_by_options[] = array(
                    'id' => $detail_to_serie_datas_options['detail_id'],
                    'title' => $datamodel_details_descriptions[$detail_to_serie_datas_options['detail_id']]['title'],
                    'abbreviation' => $datamodel_details_descriptions[$detail_to_serie_datas_options['detail_id']]['abbreviation'],
                    'sort_icon' => $datamodel_details_descriptions[$detail_to_serie_datas_options['detail_id']]['sort_icon'],
                );
            }
        }

        return $order_by_options;
    }

    public function seriesFillColors(\TechPromux\DynamicReportBundle\Entity\Component $component, array $result) {

        $chart_series_colors = array();

        $settings = $component->getSettings();

        $series_datasets_type = $settings['chart_options']['series_datasets_type'];

        if ($series_datasets_type == 'crossed_datasets') {
            $default_colors = $this->getDefaultColorsForSeries();

            $i = 0;
            foreach ($result['data'] as $series) {
                $chart_series_colors[] = $default_colors[$i % count($default_colors)];
                $i++;
            }
        } else
        if ($series_datasets_type == 'multiple_datasets') {
            $details_for_multiple_datasets_options = $settings['details_for_multiple_datasets'];
            foreach ($details_for_multiple_datasets_options as $opt) {
                $chart_series_colors[] = str_replace(')', '', str_replace('rgb(', '', $opt['serie_color']));
            }
        }
        return $chart_series_colors;
    }

    public function createDefaultResponse(\TechPromux\DynamicReportBundle\Entity\Component $component, array $parameters = array()) {
        $chart_parameters = array_merge($parameters, array(
            'order_by_options' => $this->detailsForOrderOptionsInChart($component),
            'chart_series_colors' => $this->seriesFillColors($component, $parameters['result'])
        ));
        return parent::createDefaultResponse($component, $chart_parameters);
    }

    public function preUpdate(\TechPromux\DynamicReportBundle\Entity\Component $component) {

        parent::preUpdate($component);

        $settings = $component->getSettings();

        $chart_dataset_type = isset($settings['chart_options']['series_datasets_type']) ? $settings['chart_options']['series_datasets_type'] : '';

        if ($chart_dataset_type == 'crossed_datasets') {
            $component->setSetting('details_for_multiple_datasets', array());
        } elseif ($chart_dataset_type == 'multiple_datasets') {
            $component->setSetting('detail_for_crossed_datasets_series', array());
            $component->setSetting('detail_for_crossed_datasets_datas', array());
        }

        return $component;
    }

    public function renderDefaultResponse(\TechPromux\DynamicReportBundle\Entity\Component $component, \Doctrine\DBAL\Query\QueryBuilder $queryBuilder) {
        
    }

}

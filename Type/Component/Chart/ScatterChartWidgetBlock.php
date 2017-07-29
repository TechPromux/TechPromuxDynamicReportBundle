<?php

namespace  TechPromux\DynamicReportBundle\Type\Component\Chart;

class ScatterChartComponentBlock extends AbstractChartComponentBlock {

    public function __construct($type) {
        parent::__construct($type);
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return 'Scatter Chart';
    }

    /**
     * @return array
     */
    public function getComponentMetadata() {
        return array(
            'name' => $this->getName(),
            'code' => $this->getType(),
            'bundle' => 'TechPromuxDynamicReportBundle',
            'icon' => 'fa-area-chart'
        );
    }

    /**
     * @return string
     */
    public function getTemplateForEditForm() {
        return 'TechPromuxDynamicReportBundle:Component:Chart/Scatter/edit.html.twig';
    }

    /**
     * @return string
     */
    public function getRenderTemplate() {
        return 'TechPromuxDynamicReportBundle:Component:Chart/Scatter/component.render.html.twig';
    }

    public function getComponentJavascripts() {
        return array_merge(parent::getComponentJavascripts(), array(
            'bundles/techpromuxcore/vendor/chartjs/Chart.Scatter.js/Chart.Scatter.min.js',
        ));
    }

    public function getHasSeriesData(\TechPromux\DynamicReportBundle\Entity\Component $component) {
        return false;
    }

    public function getDefaultCustomSettings() {

        $default_settings = parent::getDefaultCustomSettings();

        unset($default_settings['details_for_multiple_datasets']);
        unset($default_settings['detail_for_crossed_datasets_series']);
        unset($default_settings['detail_for_crossed_datasets_datas']);

        $default_settings['chart_options'] = array(
            'chart_type' => 'ScatterStroke',
            'series_type' => 'single_serie', // multiple_series
            'single_serie_color' => null,
            'width' => null,
            'height' => null,
        );

        $default_settings['detail_for_data'] = array(
            'detail_id' => null,
            'show_prefix' => true,
            'show_suffix' => true,
            'show_filter' => true,
        );

        $default_settings['detail_for_serie'] = array(
            'detail_id' => null,
            'show_prefix' => true,
            'show_suffix' => true,
            'show_filter' => true,
        );

        $default_settings['detail_for_radius'] = array(
            'detail_id' => null,
            'show_prefix' => true,
            'show_suffix' => true,
            'show_filter' => true,
        );

        return $default_settings;
    }

    public function createEditFormKeysSettings(\TechPromux\DynamicReportBundle\Entity\Component $component) {

        $keys = parent::createEditFormKeysSettings($component);

        unset($keys['details_for_multiple_datasets']);
        unset($keys['detail_for_crossed_datasets_series']);
        unset($keys['detail_for_crossed_datasets_datas']);

        $keys['chart_options'] = array('chart_options', 'sonata_type_immutable_array', array(
                'label' => $this->trans('Basic Chart Options'),
                'keys' => array(
                    array('chart_type', 'choice', array(
                            'label' => 'Scatter Chart Type',
                            'choices' => array("ScatterStroke" => $this->trans("Scatter Stroke"), "ScatterBubble" => $this->trans("Scatter Bubble")), // TODO translator y manager
                            "multiple" => false, "expanded" => false, "required" => true,
                            //'disabled' => ($component->getId() && isset($component->getSettings()['chart_options']) && isset($component->getSettings()['chart_options']['bar_chart_type'])),
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
                        )),
                    array('series_type', 'choice', array(
                            'label' => 'Series Type',
                            'choices' => array("single_serie" => $this->trans("Single Serie"), "multiple_series" => $this->trans("Multiples Series")), // TODO translator y manager
                            "multiple" => false, "expanded" => false, "required" => true,
                            //'disabled' => ($component->getId() && isset($component->getSettings()['chart_options']) && isset($component->getSettings()['chart_options']['bar_chart_type'])),
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
                        )),
                    array('single_serie_color', 'text', array(
                            'label' => 'Scatter Color',
                            "required" => false,
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
                            'read_only' => true,
                            'empty_data' => 'rgb(0,0,255)',
                            'attr' => array('class' => 'color-picker', 'style' => 'width: 140px;'),
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

        $keys[] = array('detail_for_data', 'sonata_type_immutable_array', array(
                'label' => false,
                'keys' => array(
                    array('detail_id', 'choice', array(
                            'label' => 'Detail for Vertical Labels',
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => $this->getComponentManager()->getReportManager()->choicesForNumericQueryDetailsFromSelectedQuery($component->getQuery()->getId()),
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

        $keys[] = array('detail_for_radius', 'sonata_type_immutable_array', array(
                'label' => false,
                'keys' => array(
                    array('detail_id', 'choice', array(
                            'label' => 'Detail for Bubbles Radius',
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => $this->getComponentManager()->getReportManager()->choicesForNumericQueryDetailsFromSelectedQuery($component->getQuery()->getId()),
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

        $keys[] = array('detail_for_series', 'sonata_type_immutable_array', array(
                'label' => false,
                'keys' => array(
                    array('detail_id', 'choice', array(
                            'label' => 'Detail for Series',
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

        return $keys;
    }

    public function detailsDescriptionsToFilterBy(\TechPromux\DynamicReportBundle\Entity\Component $component) {

        $settings = $component->getSettings();

        $details_for_filter = array();

        $details_ids = array();

        if ($settings['detail_for_label']['show_filter'] == true) {
            $details_ids[] = $settings['detail_for_label']['detail_id'];
        }
        if ($settings['detail_for_data']['show_filter'] == true) {
            $details_ids[] = $settings['detail_for_data']['detail_id'];
        }
        if ($settings['chart_options']['chart_type'] == 'ScatterBubble' && $settings['detail_for_radius']['show_filter'] == true) {
            $details_ids[] = $settings['detail_for_radius']['detail_id'];
        }
        if ($settings['chart_options']['series_type'] == 'multiple_series' && $settings['detail_for_series']['show_filter'] == true) {
            $details_ids[] = $settings['detail_for_series']['detail_id'];
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

        $order_by_options[] = array(
            'id' => $settings['detail_for_data']['detail_id'],
            'title' => $datamodel_details_descriptions[$settings['detail_for_data']['detail_id']]['title'],
            'abbreviation' => $datamodel_details_descriptions[$settings['detail_for_data']['detail_id']]['abbreviation'],
            'sort_icon' => $datamodel_details_descriptions[$settings['detail_for_data']['detail_id']]['sort_icon'],
        );

        if ($settings['chart_options']['chart_type'] == 'ScatterBubble') {
            $order_by_options[] = array(
                'id' => $settings['detail_for_radius']['detail_id'],
                'title' => $datamodel_details_descriptions[$settings['detail_for_radius']['detail_id']]['title'],
                'abbreviation' => $datamodel_details_descriptions[$settings['detail_for_radius']['detail_id']]['abbreviation'],
                'sort_icon' => $datamodel_details_descriptions[$settings['detail_for_radius']['detail_id']]['sort_icon'],
            );
        }
        if ($settings['chart_options']['series_type'] == 'multiple_series') {
            $order_by_options[] = array(
                'id' => $settings['detail_for_series']['detail_id'],
                'title' => $datamodel_details_descriptions[$settings['detail_for_series']['detail_id']]['title'],
                'abbreviation' => $datamodel_details_descriptions[$settings['detail_for_series']['detail_id']]['abbreviation'],
                'sort_icon' => $datamodel_details_descriptions[$settings['detail_for_series']['detail_id']]['sort_icon'],
            );
        }

        return $order_by_options;
    }

    public function seriesFillColors(\TechPromux\DynamicReportBundle\Entity\Component $component, array $result) {

        $settings = $component->getSettings();

        $chart_series_colors = array();

        $default_colors = $this->getDefaultColorsForSeries();

        if ($settings['chart_options']['series_type'] == 'multiple_series') {
            $i = 0;
            foreach ($result['data'] as $series) {
                $chart_series_colors[] = $default_colors[$i % count($default_colors)];
                $i++;
            }
        } else {
            $chart_series_colors[] = str_replace(')', '', str_replace('rgb(', '', $settings['chart_options']['single_serie_color']));
        }

        return $chart_series_colors;
    }

    public function preUpdate(\TechPromux\DynamicReportBundle\Entity\Component $component) {
        parent::preUpdate($component);
    }

    public function createExportableData(\TechPromux\DynamicReportBundle\Entity\Component $component, \Doctrine\DBAL\Query\QueryBuilder $queryBuilder) {

        // OBTAIN SETTINGS

        $settings = $component->getSettings();

        $result = $queryBuilder->execute()->fetchAll();

        $datamodel_details_descriptions = $this->getComponentManager()->getReportManager()->getDataModelManager()->descriptionForPublicDetailsFromQuery($component->getQuery()->getId());

        $data = array(
            'title' => $component->getTitle(),
            'labels' => array(),
            'data' => array(
            )
        );

        $detail_for_label_options = $settings['detail_for_label'];
        $data['labels'][] = $datamodel_details_descriptions[$detail_for_label_options['detail_id']]['title'];
        $detail_for_datas_options = $settings['detail_for_data'];
        $data['labels'][] = $datamodel_details_descriptions[$detail_for_datas_options['detail_id']]['title'];

        if ($settings['chart_options']['chart_type'] == 'ScatterBubble') {
            $detail_for_radius_options = $settings['detail_for_radius'];
            $data['labels'][] = $datamodel_details_descriptions[$detail_for_radius_options['detail_id']]['title'];
        }
        if ($settings['chart_options']['series_type'] == 'single_serie') {
            $data['data'][$datamodel_details_descriptions[$detail_for_datas_options['detail_id']]['title']] = array();
        } else { //if ($settings['chart_options']['series_type'] == 'multiple_series') 
            $detail_for_series_options = $settings['detail_for_series'];
            $data['labels'][] = $datamodel_details_descriptions[$detail_for_series_options['detail_id']]['title'];
        }



        foreach ($result as $i => $row) {

            $x_value = $row[$datamodel_details_descriptions[$detail_for_label_options['detail_id']]['sql_alias']];
            $x_format = $datamodel_details_descriptions[$detail_for_label_options['detail_id']]['format'];
            //$x_prefix = $detail_for_label_options['show_prefix'] ? $datamodel_details_descriptions[$detail_for_label_options['detail_id']]['prefix'] : '';
            //$x_suffix = $detail_for_label_options['show_suffix'] ? $datamodel_details_descriptions[$detail_for_label_options['detail_id']]['suffix'] : '';
            $x_value_formatted = $this->getComponentManager()->getReportManager()->formatValue($x_value, $x_format, '', '', true);


            $y_value = $row[$datamodel_details_descriptions[$detail_for_datas_options['detail_id']]['sql_alias']];
            $y_format = $datamodel_details_descriptions[$detail_for_datas_options['detail_id']]['format'];
            //$y_prefix = $detail_for_datas_options['show_prefix'] ? $datamodel_details_descriptions[$detail_for_datas_options['detail_id']]['prefix'] : '';
            //$y_suffix = $detail_for_datas_options['show_prefix'] ? $datamodel_details_descriptions[$detail_for_datas_options['detail_id']]['suffix'] : '';
            $y_value_formatted = $this->getComponentManager()->getReportManager()->formatValue($y_value, $y_format, '', '', true);

            $point_data = array(
                'x' => $x_value_formatted,
                'y' => $y_value_formatted
            );

            if ($settings['chart_options']['chart_type'] == 'ScatterBubble') {
                $r_value = $row[$datamodel_details_descriptions[$detail_for_radius_options['detail_id']]['sql_alias']];
                $r_format = $datamodel_details_descriptions[$detail_for_radius_options['detail_id']]['format'];
                //$r_prefix = $detail_for_radius_options['show_prefix'] ? $datamodel_details_descriptions[$detail_for_radius_options['detail_id']]['prefix'] : '';
                //$r_suffix = $detail_for_radius_options['show_prefix'] ? $datamodel_details_descriptions[$detail_for_radius_options['detail_id']]['suffix'] : '';
                $r_value_formatted = $this->getComponentManager()->getReportManager()->formatValue($r_value, $r_format, '', '', true);
                $point_data['r'] = $r_value_formatted;
            }

            if ($settings['chart_options']['series_type'] == 'single_serie') {
                $data['data'][$datamodel_details_descriptions[$detail_for_datas_options['detail_id']]['title']][] = $point_data;
            } else { //if ($settings['chart_options']['series_type'] == 'multiple_series') 
                $s_value = $row[$datamodel_details_descriptions[$detail_for_series_options['detail_id']]['sql_alias']];
                $s_format = $datamodel_details_descriptions[$detail_for_series_options['detail_id']]['format'];
                $s_prefix = $detail_for_series_options['show_prefix'] ? $datamodel_details_descriptions[$detail_for_series_options['detail_id']]['prefix'] : '';
                $s_suffix = $detail_for_series_options['show_prefix'] ? $datamodel_details_descriptions[$detail_for_series_options['detail_id']]['suffix'] : '';
                $s_value_formatted = $this->getComponentManager()->getReportManager()->formatValue($s_value, $s_format, $s_prefix, $s_suffix);
                if (!isset($data['data'][$s_value_formatted]))
                    $data['data'][$s_value_formatted] = array();
                $data['data'][$s_value_formatted][] = $point_data;
            }
        }

        return $data;
    }

    public function renderDefaultResponse(\TechPromux\DynamicReportBundle\Entity\Component $component, \Doctrine\DBAL\Query\QueryBuilder $queryBuilder) {

        $result = $this->createExportableData($component, $queryBuilder);

        $chart_series_colors = $this->seriesFillColors($component, $result);

        //---------------------------

        $json_data = array();

        $i = 0;
        foreach ($result['data'] as $serie => $data) {
            $float_data = array();
            foreach ($data as $point) {
                if (!isset($point['r'])) {
                    $float_data[] = array(
                        'x' => floatval($point['x']),
                        'y' => floatval($point['y']),
                    );
                } else {
                    $float_data[] = array(
                        'x' => floatval($point['x']),
                        'y' => floatval($point['y']),
                        'r' => floatval($point['r'])
                    );
                }
            }
            $json_data[] = array(
                'label' => $serie,
                'strokeColor' => "rgba(" . $chart_series_colors[$i] . ",0.5)",
                'data' => $float_data
            );
            $i++;
        }

        if ($this->getComponentManager()->getReportManager()->getCurrentRequest()->isXmlHttpRequest()) {
            return new \Symfony\Component\HttpFoundation\JsonResponse($json_data, 200);
        } else {
            return $this->createDefaultResponse($component, array('result' => $result, 'chart_data' => json_encode($json_data)));
        }
    }

}

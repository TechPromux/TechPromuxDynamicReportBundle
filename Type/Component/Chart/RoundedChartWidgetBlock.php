<?php

namespace TechPromux\Bundle\DynamicReportBundle\Type\Component\Chart;

class RoundedChartComponentBlock extends AbstractChartComponentBlock {

    public function __construct($type) {
        parent::__construct($type);
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return 'Rounded Chart';
    }

    /**
     * @return array
     */
    public function getComponentMetadata() {
        return array(
            'name' => $this->getName(),
            'code' => $this->getType(),
            'bundle' => 'TechPromuxDynamicReportBundle',
            'icon' => 'fa-pie-chart'
        );
    }

    /**
     * @return string
     */
    public function getTemplateForEditForm() {
        return 'TechPromuxDynamicReportBundle:Component:Chart/Rounded/edit.html.twig';
    }

    /**
     * @return string
     */
    public function getRenderTemplate() {
        return 'TechPromuxDynamicReportBundle:Component:Chart/Rounded/component.render.html.twig';
    }

    public function getHasSeriesData(\TechPromux\Bundle\DynamicReportBundle\Entity\Component $component) {
        return false;
    }

    public function getDefaultCustomSettings() {

        $default_settings = parent::getDefaultCustomSettings();

        unset($default_settings['details_for_multiple_datasets']);
        unset($default_settings['detail_for_crossed_datasets_series']);
        unset($default_settings['detail_for_crossed_datasets_datas']);

        $default_settings['chart_options'] = array(
            'chart_type' => 'Pie',
            'width' => null,
            'height' => null,
        );

        $default_settings['detail_for_data'] = array(
            'detail_id' => null,
            'show_prefix' => true,
            'show_suffix' => true,
            'show_filter' => true,
        );

        return $default_settings;
    }

    public function createEditFormKeysSettings(\TechPromux\Bundle\DynamicReportBundle\Entity\Component $component) {

        $keys = parent::createEditFormKeysSettings($component);

        unset($keys['details_for_multiple_datasets']);
        unset($keys['detail_for_crossed_datasets_series']);
        unset($keys['detail_for_crossed_datasets_datas']);

        $keys['chart_options'] = array('chart_options', 'sonata_type_immutable_array', array(
                'label' => $this->trans('Basic Chart Options'),
                'keys' => array(
                    array('chart_type', 'choice', array(
                            'label' => 'Rounded Chart Type',
                            'choices' => array("Pie" => $this->trans("Pie"), "Doughnut" => $this->trans("Doughnut"), "PolarArea" => $this->trans("Polar Area")), // TODO translator y manager
                            "multiple" => false, "expanded" => false, "required" => true,
                            //'disabled' => ($component->getId() && isset($component->getSettings()['chart_options']) && isset($component->getSettings()['chart_options']['bar_chart_type'])),
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
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
                            'label' => 'Detail for Datas',
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

        return $keys;
    }

    public function preUpdate(\TechPromux\Bundle\DynamicReportBundle\Entity\Component $component) {

        parent::preUpdate($component);
    }

    public function detailsDescriptionsToFilterBy(\TechPromux\Bundle\DynamicReportBundle\Entity\Component $component) {

        $settings = $component->getSettings();

        $details_for_filter = array();

        $details_ids = array();

        if ($settings['detail_for_label']['show_filter'] == true) {
            $details_ids[] = $settings['detail_for_label']['detail_id'];
        }
        if ($settings['detail_for_data']['show_filter'] == true) {
            $details_ids[] = $settings['detail_for_data']['detail_id'];
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

    public function detailsForOrderOptionsInChart(\TechPromux\Bundle\DynamicReportBundle\Entity\Component $component) {
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

        return $order_by_options;
    }

    public function seriesFillColors(\TechPromux\Bundle\DynamicReportBundle\Entity\Component $component, array $result) {

        $chart_series_colors = array();

        $default_colors = $this->getDefaultColorsForSeries();

        $i = 0;
        foreach ($result['data'][0] as $values) {
            $chart_series_colors[] = $default_colors[$i % count($default_colors)];
            $i++;
        }
        return $chart_series_colors;
    }

    public function createExportableData(\TechPromux\Bundle\DynamicReportBundle\Entity\Component $component, \Doctrine\DBAL\Query\QueryBuilder $queryBuilder) {

        // OBTAIN SETTINGS

        $settings = $component->getSettings();

        $result = $queryBuilder->execute()->fetchAll();

        $datamodel_details_descriptions = $this->getComponentManager()->getReportManager()->getDataModelManager()->descriptionForPublicDetailsFromQuery($component->getQuery()->getId());

        $data = array(
            'title' => $component->getTitle(),
            'labels' => array(),
            'data' => array(
                array()
            )
        );

        $detail_for_label_options = $settings['detail_for_label'];
        $detail_for_datas_options = $settings['detail_for_data'];

        foreach ($result as $row) {

            $col_label = $row[$datamodel_details_descriptions[$detail_for_label_options['detail_id']]['sql_alias']];
            $l_format = $datamodel_details_descriptions[$detail_for_label_options['detail_id']]['format'];
            $l_prefix = $detail_for_label_options['show_prefix'] ? $datamodel_details_descriptions[$detail_for_label_options['detail_id']]['prefix'] : '';
            $l_suffix = $detail_for_label_options['show_suffix'] ? $datamodel_details_descriptions[$detail_for_label_options['detail_id']]['suffix'] : '';
            $col_label_formatted = $this->getComponentManager()->getReportManager()->formatValue($col_label, $l_format, $l_prefix, $l_suffix);
            $data['labels'][$col_label_formatted] = $col_label_formatted;

            $data_value = $row[$datamodel_details_descriptions[$detail_for_datas_options['detail_id']]['sql_alias']];
            $d_format = $datamodel_details_descriptions[$detail_for_datas_options['detail_id']]['format'];
            //$d_prefix = $detail_for_datas_options['show_prefix'] ? $datamodel_details_descriptions[$detail_for_datas_options['detail_id']]['prefix'] : '';
            //$d_suffix = $detail_for_datas_options['show_prefix'] ? $datamodel_details_descriptions[$detail_for_datas_options['detail_id']]['suffix'] : '';
            $data_value_formatted = $this->getComponentManager()->getReportManager()->formatValue($data_value, $d_format, '', '', true);
            $data['data'][0][] = $data_value_formatted;
        }

        return $data;
    }

    public function renderDefaultResponse(\TechPromux\Bundle\DynamicReportBundle\Entity\Component $component, \Doctrine\DBAL\Query\QueryBuilder $queryBuilder) {

        $result = $this->createExportableData($component, $queryBuilder);

        $chart_series_colors = $this->seriesFillColors($component, $result);

        //---------------------------

        $json_data = array();

        $i = 0;
        foreach ($result['labels'] as $label) {
            $json_data[] = array(
                'label' => $label,
                'value' => floatval($result['data'][0][$i]),
                'color' => "rgba(" . $chart_series_colors[$i] . ",1)",
                'highlight' => "rgba(" . $chart_series_colors[$i] . ",0.7)",
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

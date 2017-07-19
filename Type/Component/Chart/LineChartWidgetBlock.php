<?php

namespace TechPromux\Bundle\DynamicReportBundle\Type\Component\Chart;

class LineChartComponentBlock extends AbstractChartComponentBlock {

    public function __construct($type) {
        parent::__construct($type);
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return 'Line Chart';
    }

    /**
     * @return array
     */
    public function getComponentMetadata() {
        return array(
            'name' => $this->getName(),
            'code' => $this->getType(),
            'bundle' => 'TechPromuxDynamicReportBundle',
            'icon' => 'fa-line-chart'
        );
    }

    /**
     * @return string
     */
    public function getTemplateForEditForm() {
        return 'TechPromuxDynamicReportBundle:Component:Chart/Line/edit.html.twig';
    }

    /**
     * @return string
     */
    public function getRenderTemplate() {
        return 'TechPromuxDynamicReportBundle:Component:Chart/Line/component.render.html.twig';
    }

    public function getDefaultCustomSettings() {

        $default_settings = parent::getDefaultCustomSettings();

        $default_settings['chart_options'] = array(
            'chart_type' => 'Line',
            'series_datasets_type' => 'multiple_datasets', // 'cross_datasets'
            'width' => null,
            'height' => null,
            'limit_min' => null,
            'limit_max' => null,
            'limit_min_serie_color' => null,
            'limit_max_serie_color' => null,
        );

        return $default_settings;
    }

    public function createEditFormKeysSettings(\TechPromux\Bundle\DynamicReportBundle\Entity\Component $component) {

        $keys = parent::createEditFormKeysSettings($component);

        $keys['chart_options'] = array('chart_options', 'sonata_type_immutable_array', array(
                'label' => $this->trans('Basic Chart Options'),
                'keys' => array(
                    array('chart_type', 'hidden', array(
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'hide'),
                        )),
                    array('series_datasets_type', 'choice', array(
                            'label' => 'Series Datasets Type',
                            'choices' => array("crossed_datasets" => $this->trans("Crossed Datasets"), "multiple_datasets" => $this->trans("Multiple Datasets")),
                            "multiple" => false, "expanded" => false, "required" => true,
                            //'disabled' => ($component->getId() && isset($component->getSettings()['chart_options']) && isset($component->getSettings()['chart_options']['series_datasets_type'])),
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
                    array('limit_min', 'number', array(
                            'label' => 'Min Value Indicator',
                            "required" => false,
                            "label_attr" => array(
                                //'class' => 'pull-left',
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'
                            ),
                        )),
                    array('limit_min_serie_color', 'text', array(
                            'label' => 'Min Serie Color',
                            "required" => false,
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
                            'read_only' => true,
                            'empty_data' => 'rgb(0,0,255)',
                            'attr' => array('class' => 'color-picker', 'style' => 'width: 140px;'),
                        )),
                    array('limit_max', 'number', array(
                            'label' => 'Max Value Indicator',
                            "required" => false,
                            "label_attr" => array(
                                // 'class' => 'pull-left',
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'
                            ),
                        )),
                    array('limit_max_serie_color', 'text', array(
                            'label' => 'Max Serie Color',
                            "required" => false,
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
                            'read_only' => true,
                            'empty_data' => 'rgb(255,0,0)',
                            'attr' => array('class' => 'color-picker', 'style' => 'width: 140px;'),
                        )),
                )
        ));

        return $keys;
    }

    public function preUpdate(\TechPromux\Bundle\DynamicReportBundle\Entity\Component $component) {

        parent::preUpdate($component);

        return $component;
    }

    public function renderDefaultResponse(\TechPromux\Bundle\DynamicReportBundle\Entity\Component $component, \Doctrine\DBAL\Query\QueryBuilder $queryBuilder) {

        $result = $this->createExportableData($component, $queryBuilder);

        $settings = $component->getSettings();

        $chart_series_colors = $this->seriesFillColors($component, $result);

        //---------------------------

        $json_data = array();

        $json_data['labels'] = array();

        foreach ($result['labels'] as $label) {
            $json_data['labels'][] = $label['label'];
        }

        $json_data['datasets'] = array();

        $i = 0;
        foreach ($result['data'] as $serie => $data) {
            $dataset = array();

            $dataset['label'] = $serie;
            $dataset['fillColor'] = "rgba(" . $chart_series_colors[$i] . ",0.0)";
            $dataset['strokeColor'] = "rgba(" . $chart_series_colors[$i] . ",1)";
            $dataset['pointColor'] = "rgba(" . $chart_series_colors[$i] . ",1)";
            $dataset['pointStrokeColor'] = "#fff";
            $dataset['pointHighlightFill'] = "#fff";
            $dataset['pointHighlightStroke'] = "rgba(" . $chart_series_colors[$i] . ",1)";
            $dataset['data'] = array();
            foreach ($data as $value) {
                $dataset['data'][] = $value;
            }

            $json_data['datasets'][] = $dataset;
            $i++;
        }

        if ($settings['chart_options']['limit_min'] != null && $settings['chart_options']['limit_min'] != '') {
            $dataset = array();

            $dataset['label'] = $this->trans("Min") . ": " . $settings['chart_options']['limit_min'];
            $dataset['fillColor'] = "rgba(" . str_replace(')', '', str_replace('rgb(', '', $settings['chart_options']['limit_min_serie_color'])) . ",0.0)";
            $dataset['strokeColor'] = "rgba(" . str_replace(')', '', str_replace('rgb(', '', $settings['chart_options']['limit_min_serie_color'])) . ",1)";
            $dataset['pointColor'] = "rgba(" . str_replace(')', '', str_replace('rgb(', '', $settings['chart_options']['limit_min_serie_color'])) . ",0)";
            $dataset['pointStrokeColor'] = "rgba(" . str_replace(')', '', str_replace('rgb(', '', $settings['chart_options']['limit_min_serie_color'])) . ",0.0)";
            $dataset['pointHighlightFill'] = "rgba(" . str_replace(')', '', str_replace('rgb(', '', $settings['chart_options']['limit_min_serie_color'])) . ",0.0)";
            $dataset['pointHighlightStroke'] = "rgba(" . str_replace(')', '', str_replace('rgb(', '', $settings['chart_options']['limit_min_serie_color'])) . ",0.0)";
            $dataset['data'] = array();
            foreach ($result['labels'] as $label) {
                $dataset['data'][] = $settings['chart_options']['limit_min'];
            }

            $json_data['datasets'][] = $dataset;
        }
        if ($settings['chart_options']['limit_max'] != null && $settings['chart_options']['limit_max'] != '') {
            $dataset = array();

            $dataset['label'] = $this->trans("Max") . ": " . $settings['chart_options']['limit_max'];
            $dataset['fillColor'] = "rgba(" . str_replace(')', '', str_replace('rgb(', '', $settings['chart_options']['limit_max_serie_color'])) . ",0)";
            $dataset['strokeColor'] = "rgba(" . str_replace(')', '', str_replace('rgb(', '', $settings['chart_options']['limit_max_serie_color'])) . ",1)";
            $dataset['pointColor'] = "rgba(" . str_replace(')', '', str_replace('rgb(', '', $settings['chart_options']['limit_max_serie_color'])) . ",0)";
            $dataset['pointStrokeColor'] = "rgba(" . str_replace(')', '', str_replace('rgb(', '', $settings['chart_options']['limit_max_serie_color'])) . ",0.0)";
            $dataset['pointHighlightFill'] = "rgba(" . str_replace(')', '', str_replace('rgb(', '', $settings['chart_options']['limit_max_serie_color'])) . ",0.0)";
            $dataset['pointHighlightStroke'] = "rgba(" . str_replace(')', '', str_replace('rgb(', '', $settings['chart_options']['limit_max_serie_color'])) . ",0.0)";
            $dataset['data'] = array();
            foreach ($result['labels'] as $label) {
                $dataset['data'][] = $settings['chart_options']['limit_max'];
            }
            $json_data['datasets'][] = $dataset;
        }
        if ($this->getComponentManager()->getReportManager()->getCurrentRequest()->isXmlHttpRequest()) {
            return new \Symfony\Component\HttpFoundation\JsonResponse($json_data, 200);
        } else {
            return $this->createDefaultResponse($component, array('result' => $result, 'chart_data' => json_encode($json_data)));
        }
    }

}

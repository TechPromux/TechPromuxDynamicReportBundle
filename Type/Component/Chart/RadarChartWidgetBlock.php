<?php

namespace TechPromux\Bundle\DynamicReportBundle\Type\Component\Chart;

class RadarChartComponentBlock extends AbstractChartComponentBlock {

    public function __construct($type) {
        parent::__construct($type);
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return 'Radar Chart';
    }

    /**
     * @return array
     */
    public function getComponentMetadata() {
        return array(
            'name' => $this->getName(),
            'code' => $this->getType(),
            'bundle' => 'TechPromuxDynamicReportBundle',
            'icon' => 'fa-diamond'
        );
    }

    /**
     * @return string
     */
    public function getTemplateForEditForm() {
        return 'TechPromuxDynamicReportBundle:Component:Chart/Radar/edit.html.twig';
    }

    /**
     * @return string
     */
    public function getRenderTemplate() {
        return 'TechPromuxDynamicReportBundle:Component:Chart/Radar/component.render.html.twig';
    }

    public function getDefaultCustomSettings() {
        $default_settings = parent::getDefaultCustomSettings();

        $default_settings['chart_options'] = array(
            'chart_type' => 'Radar',
            'series_datasets_type' => 'multiple_datasets', // 'cross_datasets'
            'width' => null,
            'height' => null,
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
                            'choices' => array("crossed_datasets" => $this->trans("Crossed Datasets"), "multiple_datasets" => $this->trans("Multiple Datasets")), // TODO translator y manager
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
            $dataset['fillColor'] = "rgba(" . $chart_series_colors[$i] . ",0.2)";
            $dataset['strokeColor'] = "rgba(" . $chart_series_colors[$i] . ",1)";
            $dataset['pointColor'] = "rgba(" . $chart_series_colors[$i] . ",1)";
            $dataset['pointStrokeColor'] = "#fff";
            $dataset['pointHighlightFill'] = "#fff";
            $dataset['pointHighlightStroke'] = "rgba(" . $chart_series_colors[$i] . ",1)";
            $dataset['data'] = array();

            foreach ($data as $value) {
                $dataset['data'][] = floatval($value);
            }

            $json_data['datasets'][] = $dataset;
            $i++;
        }

        if ($this->getComponentManager()->getReportManager()->getCurrentRequest()->isXmlHttpRequest()) {
            return new \Symfony\Component\HttpFoundation\JsonResponse($json_data, 200);
        } else {
            return $this->createDefaultResponse($component, array('result' => $result, 'chart_data' => json_encode($json_data)));
        }
    }

}

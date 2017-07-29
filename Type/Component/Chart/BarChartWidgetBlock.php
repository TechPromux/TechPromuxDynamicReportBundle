<?php

namespace  TechPromux\DynamicReportBundle\Type\Component\Chart;

class BarChartComponentBlock extends AbstractChartComponentBlock {

    public function __construct($type) {
        parent::__construct($type);
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return 'Bar Chart';
    }

    /**
     * @return array
     */
    public function getComponentMetadata() {
        return array(
            'name' => $this->getName(),
            'code' => $this->getType(),
            'bundle' => 'TechPromuxDynamicReportBundle',
            'icon' => 'fa-bar-chart'
        );
    }

    /**
     * @return string
     */
    public function getTemplateForEditForm() {
        return 'TechPromuxDynamicReportBundle:Component:Chart/Bar/edit.html.twig';
    }

    /**
     * @return string
     */
    public function getRenderTemplate() {
        return 'TechPromuxDynamicReportBundle:Component:Chart/Bar/component.render.html.twig';
    }

    public function getComponentJavascripts() {
        return array_merge(parent::getComponentJavascripts(), array(
            'bundles/techpromuxcore/vendor/chartjs/Chart.StackedBar.js/src/Chart.StackedBar.js',
        ));
    }

    public function getDefaultCustomSettings() {
        $default_settings = parent::getDefaultCustomSettings();

        $default_settings['chart_options'] = array(
            'chart_type' => 'Bar',
            'stacked_chart_bar_presentation' => 'series_bar',
            'width' => null,
            'height' => null,
        );

        return $default_settings;
    }

    public function createEditFormKeysSettings(\TechPromux\DynamicReportBundle\Entity\Component $component) {

        $keys = parent::createEditFormKeysSettings($component);

        $keys['chart_options'] = array('chart_options', 'sonata_type_immutable_array', array(
                'label' => $this->trans('Basic Chart Options'),
                'keys' => array(
                    array('chart_type', 'choice', array(
                            'label' => 'Bar Chart Type',
                            'choices' => array("Bar" => $this->trans("Bar"), "StackedBar" => $this->trans("Stacked Bar")), // TODO translator y manager
                            "multiple" => false, "expanded" => false, "required" => true,
                            //'disabled' => ($component->getId() && isset($component->getSettings()['chart_options']) && isset($component->getSettings()['chart_options']['chart_type'])),
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
                        )),
                    array('stacked_chart_bar_presentation', 'choice', array(
                            'label' => 'Stacked Presentation',
                            'choices' => array("absolute_bar" => $this->trans("Absolute"), "relative_bar" => $this->trans("Relative (Percentage)")), // TODO translator y manager
                            "multiple" => false, "expanded" => false, "required" => true,
                            //'disabled' => ($component->getId() && isset($component->getSettings()['chart_options']) && isset($component->getSettings()['chart_options']['series_datasets_type'])),
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
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

    public function preUpdate(\TechPromux\DynamicReportBundle\Entity\Component $component) {

        parent::preUpdate($component);

        return $component;
    }

    public function renderDefaultResponse(\TechPromux\DynamicReportBundle\Entity\Component $component, \Doctrine\DBAL\Query\QueryBuilder $queryBuilder) {

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
            $dataset['fillColor'] = "rgba(" . $chart_series_colors[$i] . ",0.6)";
            $dataset['strokeColor'] = "rgba(" . $chart_series_colors[$i] . ",0.9)";
            $dataset['highlightFill'] = "rgba(" . $chart_series_colors[$i] . ",0.5)";
            $dataset['highlightStroke'] = "rgba(" . $chart_series_colors[$i] . ",1)";
            $dataset['data'] = array();

            foreach ($data as $value) {
                $dataset['data'][] = floatval($value);
            }

            $json_data['datasets'][] = $dataset;
            $i++;
        }

        // calcular porcientos
        /*
          if ($settings['chart_options']['chart_type'] == "StackedBar" && $settings['chart_options']['stacked_chart_value_presentation'] == "percentage_value") {
          foreach ($json_data['labels'] as $i => $label) {
          $total = 0;
          for ($j = 0; $j < count($json_data['datasets']); $j++) {
          $total += floatval($json_data['datasets'][$j]['data'][$i]);
          }
          for ($j = 0; $j < count($json_data['datasets']); $j++) {
          $json_data['datasets'][$j]['data'][$i] = floatval(number_format(($total != 0 ? (floatval($json_data['datasets'][$j]['data'][$i]) * 100 / $total) : 0), 2));
          }
          }
          }
         */

        if ($this->getComponentManager()->getReportManager()->getCurrentRequest()->isXmlHttpRequest()) {
            return new \Symfony\Component\HttpFoundation\JsonResponse($json_data, 200);
        } else {
            return $this->createDefaultResponse($component, array('result' => $result, 'chart_data' => json_encode($json_data)));
        }
    }

}

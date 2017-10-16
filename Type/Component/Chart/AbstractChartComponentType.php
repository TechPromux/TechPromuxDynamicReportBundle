<?php

namespace TechPromux\DynamicReportBundle\Type\Component\Chart;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TechPromux\DynamicReportBundle\Entity\Component;
use TechPromux\DynamicReportBundle\Type\Component\DataModel\AbstractDataModelComponentType;

abstract class AbstractChartComponentType extends AbstractDataModelComponentType
{
    /**
     * @return string
     */
    public function getGroupName()
    {
        return 'techpromux.charts';
    }

    public function getExportablesFormats()
    {
        return array_merge(
            parent::getExportablesFormats(),
            ['png' => 'png']
        );
    }

    public function getExportablesFormatsIconsClasses()
    {
        return array_merge(
            parent::getExportablesFormatsIconsClasses(),
            ['png' => 'fa-file-image-o']
        );
    }

    /**
     * @return array
     */
    public function getDefaultCustomSettings()
    {
        $default_settings = array();

        $default_settings['chart_options'] = array(
            'chart_type' => null, // 'cross_datasets'
            'width' => null,
            'height' => null,
        );

        $default_settings['colors_for_series'] = array();

        $default_colors = $this->getDefaultColorsForSeries();

        foreach ($default_colors as $color) {
            $default_settings['colors_for_series'][] = array(
                'serie_color' => 'rgb(' . $color . ')'
            );
        }

        return $default_settings;
    }

    /**
     * @return boolean
     */
    public function getHasDataModelDataset()
    {
        return true;
    }

    /**
     * 'all', 'number', 'datetime', 'number_datetime'
     *
     * @return string
     */
    public function getSupportedDataTypeFromDataModelDetails()
    {
        return 'number';
    }

    /**
     * @param Component $component
     * @param array $options
     */
    protected function getChartTypeCustomSettingsKeyForEditForm(Component $component, $options = array())
    {
        return array('chart_type', 'hidden', array(
            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'hide'),
        ));
    }

    /**
     * @param Component $component
     * @param array $options
     * @return array
     */
    public function createCustomSettingsKeysForEditForm(Component $component, $options = array())
    {
        $keys = array();

        $keys['chart_options'] = array('chart_options', 'sonata_type_immutable_array', array(
            //'label' => $this->trans('Basic Chart Options'),
            'keys' =>
                array_merge(
                    [
                        'chart_type' => $this->getChartTypeCustomSettingsKeyForEditForm($component, $options),
                        'width' => array('width', 'number', array(
                            //'label' => 'Width',
                            "required" => false,
                            "label_attr" => array(
                                //'class' => 'pull-left',
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1'
                            ),
                            'attr' => array('placeholder' => 'px', 'style' => 'width: 70px;'),
                            'translation_domain' => $this->getBundleName()
                        )),
                        'height' => array('height', 'number', array(
                            //'label' => 'Height',
                            "required" => false,
                            "label_attr" => array(
                                // 'class' => 'pull-left',
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1'
                            ),
                            'attr' => array('placeholder' => 'px', 'style' => 'width: 70px;'),
                            'translation_domain' => $this->getBundleName()
                        )),
                    ],
                    $this->getAllowMinMaxSerieType($component) ?
                        [
                            'limit_min' => array('limit_min', 'number', array(
                                //'label' => 'Min Value Indicator',
                                "required" => false,
                                "label_attr" => array(
                                    //'class' => 'pull-left',
                                    'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'
                                ),
                                'translation_domain' => $this->getBundleName()
                            )),
                            'limit_min_serie_color' => array('limit_min_serie_color', 'text', array(
                                //'label' => 'Min Serie Color',
                                "required" => false,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
                                //'read_only' => true,
                                'empty_data' => 'rgb(0,0,255)',
                                'attr' => array('class' => 'color-picker', 'style' => 'width: 140px;'),
                                'translation_domain' => $this->getBundleName()
                            )),
                            'limit_max' => array('limit_max', 'number', array(
                                //'label' => 'Max Value Indicator',
                                "required" => false,
                                "label_attr" => array(
                                    // 'class' => 'pull-left',
                                    'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'
                                ),
                                'translation_domain' => $this->getBundleName()
                            )),
                            'limit_max_serie_color' => array('limit_max_serie_color', 'text', array(
                                //'label' => 'Max Serie Color',
                                "required" => false,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
                                //'read_only' => true,
                                'empty_data' => 'rgb(255,0,0)',
                                'attr' => array('class' => 'color-picker', 'style' => 'width: 140px;'),
                                'translation_domain' => $this->getBundleName()
                            )),
                        ] : []
                )
        ));

        $keys['colors_for_series'] = array('colors_for_series', 'sonata_type_native_collection', array(
            'entry_type' => 'sonata_type_immutable_array',
            'allow_add' => true,
            'allow_delete' => true,
            'entry_options' => array(
                'keys' => array(
                    array('serie_color', 'text', array(
                        //'label' => 'Serie Color',
                        "required" => false,
                        //"label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-3'),
                        //'read_only' => true,
                        'empty_data' => 'rgb(0,0,0)',
                        'attr' => array('class' => 'color-picker', 'style' => 'width: 140px;'),
                        'translation_domain' => $this->getBundleName()
                    )),
                )
            )
        ));

        return $keys;
    }

    /**
     * @param Component $component
     * @return mixed
     */
    protected abstract function getChartType(Component $component);

    /**
     * @param Component $component
     * @return string
     */
    protected function getMinMaxSerieType(Component $component)
    {
        return 'line';
    }

    /**
     * @param Component $component
     * @return bool
     */
    protected function getAllowMinMaxSerieType(Component $component)
    {
        return true;
    }

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $parameters
     * @param bool $full_exportable_data
     * @return array
     */
    protected function getMergedDataParametersAndSettings(Request $request = null, Component $component, array $parameters = array(), $full_exportable_data = false)
    {
        $all = parent::getMergedDataParametersAndSettings($request, $component, $parameters, $full_exportable_data); // TODO: Change the autogenerated stub

        $all = $this->getMergedChartDataParametersAndSettings($request, $component, $all, $full_exportable_data);

        return $all;
    }

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $all
     * @param bool $full_exportable_data
     * @return array
     */
    protected function getMergedChartDataParametersAndSettings(Request $request = null, Component $component, array $all = array(), $full_exportable_data = false)
    {
        $all['settings']['_chart_data'] = [
            'labels' => $all['settings']['_labels'],
            'datasets' => []
        ];

        $all['settings']['_chart_type'] = $this->getChartType($component);

        $formater_helper = $all['formatter_helper'];

        $chart_series_colors = $all['settings']['colors_for_series'];

        $i = 0;
        foreach ($all['settings']['_series'] as $serie_name => $datas) {
            $serie_color = str_replace(')', '', str_replace('rgb(', '', $chart_series_colors[$i]['serie_color']));
            $dataset = [
                'label' => $serie_name,
                'type' => $all['settings']['_chart_type'],
                'fill' => false,
                'backgroundColor' => "rgba(" . $serie_color . ",1)",
                'borderColor' => "rgba(" . $serie_color . ",1)",
                'data' => [],
            ];

            if (!$this->getHasDataModelDatasetMultipleDatas() && isset($all['settings']['_data_crossed_function'])) {
                foreach ($all['settings']['_labels'] as $label) {
                    $label_data = isset($datas[$label]) ? $datas[$label] : array();
                    $dataset['data'][] = $formater_helper->summarizeValues($all['settings']['_data_crossed_function'], $label_data);
                }
            } else {
                $dataset['data'] = $datas;
            }

            $all['settings']['_chart_data']['datasets'][] = $dataset;
            $i = ($i + 1) % count($chart_series_colors);
        }

        if ($this->getAllowMinMaxSerieType($component)) {
            $min_max_serie_type = $this->getMinMaxSerieType($component);
            if (!empty($min_max_serie_type) && !empty($all['settings']['chart_options']['limit_min'])) {
                $limit_min_color = $all['settings']['chart_options']['limit_min_serie_color'];
                if (empty($limit_min_color)) {
                    $limit_min_color = $chart_series_colors[$i];
                    $i = ($i + 1) % count($chart_series_colors);
                }
                $serie_color = str_replace(')', '', str_replace('rgb(', '', $limit_min_color));
                $dataset_min = [
                    'type' => $min_max_serie_type,
                    'label' => 'MIN',
                    'fill' => false,
                    'backgroundColor' => "rgba(" . $serie_color . ",1)",
                    'borderColor' => "rgba(" . $serie_color . ",1)",
                    'borderDash' => [5, 5],
                    'pointRadius' => 1,
                    'pointHoverRadius' => 1,
                    'data' => [],
                ];
                foreach ($all['settings']['_labels'] as $labels) {
                    $dataset_min['data'][] = $all['settings']['chart_options']['limit_min'];
                }
                $all['settings']['_chart_data']['datasets'][] = $dataset_min;
            }
            if (!empty($min_max_serie_type) && !empty($all['settings']['chart_options']['limit_max'])) {
                $limit_max_color = $all['settings']['chart_options']['limit_max_serie_color'];
                if (empty($limit_max_color)) {
                    $limit_max_color = $chart_series_colors[$i];
                    //$i = ($i + 1) % count($chart_series_colors);
                }
                $serie_color = str_replace(')', '', str_replace('rgb(', '', $limit_max_color));
                $dataset_max = [
                    'type' => $min_max_serie_type,
                    'label' => 'MAX',
                    'fill' => false,
                    'backgroundColor' => "rgba(" . $serie_color . ",1)",
                    'borderColor' => "rgba(" . $serie_color . ",1)",
                    'borderDash' => [5, 5],
                    'pointRadius' => 1,
                    'pointHoverRadius' => 1,
                    'data' => [],
                ];
                foreach ($all['settings']['_labels'] as $labels) {
                    $dataset_max['data'][] = $all['settings']['chart_options']['limit_max'];
                }
                $all['settings']['_chart_data']['datasets'][] = $dataset_max;
            }
        }

        return $all;
    }

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $parameters
     * @return string|JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function createRenderableContent(Request $request = null, Component $component, array $parameters = array())
    {
        if ($request->isXmlHttpRequest()) {

            $chart_data = $parameters['settings']['_chart_data'];

            return new JsonResponse($chart_data);
        }
        return parent::createRenderableContent($request, $component, $parameters); // TODO: Change the autogenerated stub
    }

    /**
     *
     * @return array
     */
    public function getComponentJavascripts()
    {
        return [
            'bundles/techpromuxdynamicreport/vendor/chart.js/dist/Chart.bundle.min.js',
        ];
    }

    /**
     *
     * @return array
     */
    public function getComponentStylesheets()
    {
        return [];
    }

    //--------------------------------------------------------------

    public function getDefaultColorsForSeries()
    {
        $colors = array(
            "255,128,0",
            "0,128,0",
            "0,128,128",
            "104,181,67",
            "255,255,0",
            "128,0,128",
            "245,111,199",
            "151,187,205",
            "24,32,232",
            "220,220,220",
            "255,0,0",
            "64,0,0",
            "140,166,120",
            "220,0,128",
            "65,65,65",
            "0,255,0",
            "142,215,224",
            "247,99,15",
        );
        shuffle($colors);

        return $colors;
    }
}
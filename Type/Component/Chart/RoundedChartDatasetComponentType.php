<?php

namespace TechPromux\DynamicReportBundle\Type\Component\Chart;


use Symfony\Component\HttpFoundation\Request;
use TechPromux\DynamicReportBundle\Entity\Component;

class RoundedChartDatasetComponentType extends AbstractChartComponentType
{
    public function getIconClassName()
    {
        return 'fa-pie-chart';
    }

    /**
     * @return array
     */
    public function getDefaultCustomSettings()
    {

        $default_settings = parent::getDefaultCustomSettings();

        $default_settings['chart_options'] = array(
            'chart_type' => 'pie',
            'width' => null,
            'height' => null,
        );

        return $default_settings;
    }

    /**
     * @param Component $component
     * @return string
     */
    protected function getChartType(Component $component)
    {
        $options = $component->getComponentOptions();

        $chart_type = $options['chart_options']['chart_type'];

        if ($chart_type == "Pie")
            return 'pie';
        elseif ($chart_type == "Doughnut")
            return 'doughnut';
        elseif ($chart_type == "PolarArea")
            return 'PolarArea';

        return 'pie';
    }

    /**
     * @param Component $component
     * @return null|string
     */
    protected function getMinMaxSerieType(Component $component)
    {
        return null;
    }

    /**
     * @param Component $component
     * @return bool
     */
    protected function getAllowMinMaxSerieType(Component $component)
    {
        return false;
    }

    public function getTemplateForEditForm()
    {
        return '@' . $this->getBundleName() . '/Type/Component/Chart/Rounded/edit.html.twig';
    }

    /**
     * @return string
     */
    public function getTemplateForRenderComponent()
    {
        return '@' . $this->getBundleName() . '/Type/Component/Chart/Rounded/render.html.twig';
    }

    protected function getChartTypeCustomSettingsKeyForEditForm(Component $component, $options = array())
    {
        return array('chart_type', 'choice', array(
            'choices' => array(
                'Pie' => 'Pie',
                'Doughnut' => 'Doughnut',
                'PolarArea' => 'PolarArea',
            ),
            "multiple" => false,
            "expanded" => false,
            "required" => true,
            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
        ));
    }

    /**
     * @return string
     */
    public function getId()
    {
        return 'techpromux.chart.rounded_series_single_dataset';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'chart.rounded_series_single_dataset';
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
        return false;
    }

    /**
     * @return boolean
     */
    public function getHasDataModelDatasetMultipleDatas()
    {
        return false;
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
        $all['settings']['_chart_dataset_type'] = 'single';

        $all['settings']['_chart_data'] = [
            'labels' => $all['settings']['_labels'],
            'datasets' => [
                [
                    'data' => [],
                    'backgroundColor' => [],
                    'label' => 'Dataset',
                ]
            ]
        ];

        $all['settings']['_chart_type'] = $this->getChartType($component);

        $formater_helper = $all['formatter_helper'];

        $chart_series_colors = $all['settings']['colors_for_series'];

        $i = 0;
        foreach ($all['settings']['_series'] as $serie_name => $datas) {
            $all['settings']['_chart_data']['datasets'][0]['data'][] = $formater_helper->summarizeValues($all['settings']['_data_crossed_function'], $datas);
            $all['settings']['_chart_data']['datasets'][0]['backgroundColor'][] = $chart_series_colors[$i]['serie_color'];
            $i = ($i + 1) % count($chart_series_colors);
        }

        return $all;
    }
}
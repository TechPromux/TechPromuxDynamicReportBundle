<?php

namespace TechPromux\DynamicReportBundle\Type\Component\Chart;


use Symfony\Component\HttpFoundation\Request;
use TechPromux\DynamicReportBundle\Entity\Component;

abstract class AbstractBarChartDatasetComponentType extends AbstractChartComponentType
{
    public function getIconClassName()
    {
        return 'fa-bar-chart';
    }

    /**
     * @return array
     */
    public function getDefaultCustomSettings()
    {

        $default_settings = parent::getDefaultCustomSettings();

        $default_settings['chart_options'] = array(
            'chart_type' => 'bar',
            'width' => null,
            'height' => null,
            'limit_min' => null,
            'limit_max' => null,
            'limit_min_serie_color' => null,
            'limit_max_serie_color' => null,
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

        if ($chart_type == "Bar")
            return 'bar';
        elseif ($chart_type == "Bar.Stacked")
            return 'bar';
        elseif ($chart_type == "Bar.Horizontal")
            return 'horizontalBar';
        elseif ($chart_type == "Bar.Horizontal.Stacked")
            return 'horizontalBar';

        return 'bar';
    }

    protected function getMinMaxSerieType(Component $component)
    {
        $options = $component->getComponentOptions();

        $chart_type = $options['chart_options']['chart_type'];

        if ($chart_type == "Bar.Horizontal")
            return null;
        elseif ($chart_type == "Bar.Horizontal.Stacked")
            return null;
        return 'line';
    }

    protected function getAllowMinMaxSerieType(Component $component)
    {
        $options = $component->getComponentOptions();

        $chart_type = $options['chart_options']['chart_type'];

        if ($chart_type == "Bar.Horizontal")
            return false;
        elseif ($chart_type == "Bar.Horizontal.Stacked")
            return false;
        return true;
    }

    public function getTemplateForEditForm()
    {
        return '@' . $this->getBundleName() . '/Type/Component/Chart/Bar/edit.html.twig';
    }

    /**
     * @return string
     */
    public function getTemplateForRenderComponent()
    {
        return '@' . $this->getBundleName() . '/Type/Component/Chart/Bar/render.html.twig';
    }

    protected function getChartTypeCustomSettingsKeyForEditForm(Component $component, $options = array())
    {
        return array('chart_type', 'choice', array(
            'choices' => array(
                'Bar' => 'Bar',
                'Bar.Stacked' => 'Bar.Stacked',
                'Bar.Horizontal' => 'Bar.Horizontal',
                'Bar.Horizontal.Stacked' => 'Bar.Horizontal.Stacked',
            ),
            "multiple" => false,
            "expanded" => false,
            "required" => true,
            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-xs-2'),
        ));
    }
}
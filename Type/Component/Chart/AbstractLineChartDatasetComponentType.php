<?php

namespace TechPromux\DynamicReportBundle\Type\Component\Chart;


use Symfony\Component\HttpFoundation\Request;
use TechPromux\DynamicReportBundle\Entity\Component;

abstract class AbstractLineChartDatasetComponentType extends AbstractChartComponentType
{
    public function getIconClassName()
    {
        return 'fa-line-chart';
    }

    /**
     * @return array
     */
    public function getDefaultCustomSettings()
    {

        $default_settings = parent::getDefaultCustomSettings();

        $default_settings['chart_options'] = array(
            'chart_type' => 'line',
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
        return 'line';
    }

    public function getTemplateForEditForm()
    {
        return '@' . $this->getBundleName() . '/Type/Component/Chart/Line/edit.html.twig';
    }

    /**
     * @return string
     */
    public function getTemplateForRenderComponent()
    {
        return '@' . $this->getBundleName() . '/Type/Component/Chart/Line/render.html.twig';
    }
}
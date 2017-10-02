<?php
/**
 * Created by PhpStorm.
 * User: franklin
 * Date: 20/08/2017
 * Time: 20:27
 */

namespace TechPromux\DynamicReportBundle\Type\Component\Chart;

use Symfony\Component\HttpFoundation\Request;
use TechPromux\DynamicReportBundle\Entity\Component;

class RadarChartSeriesMultipleDatasetComponentType extends AbstractRadarChartDatasetComponentType
{

    /**
     * @return string
     */
    public function getId()
    {
        return 'techpromux.chart.radar_series_multiple_dataset';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'chart.radar_series_multiple_dataset';
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
        return true;
    }


    protected function getMergedDataParametersAndSettings(Request $request = null, Component $component, array $parameters = array(), $full_exportable_data = false)
    {
        $all = parent::getMergedDataParametersAndSettings($request, $component, $parameters, $full_exportable_data); // TODO: Change the autogenerated stub

        $all['settings']['_chart_dataset_type'] = 'multiple';

        return $all;
    }

}
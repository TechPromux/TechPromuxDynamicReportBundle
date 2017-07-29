<?php

namespace  TechPromux\DynamicReportBundle\Manager;

use  TechPromux\BaseBundle\Manager\Resource\BaseResourceManager;
use  TechPromux\DynamicReportBundle\Entity\Report;

/**
 * ReportManager
 *
 */
class ReportManager extends BaseResourceManager
{
    /**
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'TechPromuxDynamicReportBundle';
    }

    /**
     * Get entity class name
     *
     * @return class
     */
    public function getResourceClass()
    {
        return Report::class;
    }

    /**
     * Get entity short name
     *
     * @return string
     */
    public function getResourceName()
    {
        return 'Report';
    }

    //------------------------------------------------------

    /**
     * @var UtilDynamicReportManager
     */
    protected $util_dynamic_report_manager;

    /**
     * @return UtilDynamicReportManager
     */
    public function getUtilDynamicReportManager()
    {
        return $this->util_dynamic_report_manager;
    }

    /**
     * @param UtilDynamicReportManager $util_dynamic_report_manager
     * @return ReportManager
     */
    public function setUtilDynamicReportManager($util_dynamic_report_manager)
    {
        $this->util_dynamic_report_manager = $util_dynamic_report_manager;
        return $this;
    }




}

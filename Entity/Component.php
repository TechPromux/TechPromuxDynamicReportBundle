<?php

namespace  TechPromux\DynamicReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use  TechPromux\BaseBundle\Entity\Resource\BaseResource;

/**
 * Component
 *
 * @ORM\Table(name="techpromux_dynamic_report_component")
 * @ORM\Entity()
 */
class Component extends BaseResource
{
    /**
     * @var string
     *
     * @ORM\Column(name="component_type", type="string", length=255)
     */
    private $componentType;

    /**
     * @var array
     *
     * @ORM\Column(name="data_options", type="json_array", nullable=true)
     */
    protected $data_options;

    /**
     * @var array
     *
     * @ORM\Column(name="component_options", type="json_array", nullable=true)
     */
    protected $component_options;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="template_container", type="string", length=255)
     */
    private $templateContainer;

    /**
     * @var Report
     *
     * @ORM\ManyToOne(targetEntity="Report", inversedBy="components")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", nullable=false)
     */
    private $report;

    /**
     * @var DataModel
     *
     * @ORM\ManyToOne(targetEntity="TechPromux\DynamicQueryBundle\Entity\DataModel")
     * @ORM\JoinColumn(name="datamodel_id", referencedColumnName="id", nullable=false)
     */
    private $datamodel;

    public function __toString()
    {
        return $this->id ? $this->getName() : '';
    }

    /**
     * Set componentType
     *
     * @param string $componentType
     *
     * @return Component
     */
    public function setComponentType($componentType)
    {
        $this->componentType = $componentType;

        return $this;
    }

    /**
     * Get componentType
     *
     * @return string
     */
    public function getComponentType()
    {
        return $this->componentType;
    }

    /**
     * Set dataOptions
     *
     * @param array $dataOptions
     *
     * @return Component
     */
    public function setDataOptions($dataOptions)
    {
        $this->data_options = $dataOptions;

        return $this;
    }

    /**
     * Get dataOptions
     *
     * @return array
     */
    public function getDataOptions()
    {
        return $this->data_options;
    }

    /**
     * Set componentOptions
     *
     * @param array $componentOptions
     *
     * @return Component
     */
    public function setComponentOptions($componentOptions)
    {
        $this->component_options = $componentOptions;

        return $this;
    }

    /**
     * Get componentOptions
     *
     * @return array
     */
    public function getComponentOptions()
    {
        return $this->component_options;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Component
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set templateContainer
     *
     * @param string $templateContainer
     *
     * @return Component
     */
    public function setTemplateContainer($templateContainer)
    {
        $this->templateContainer = $templateContainer;

        return $this;
    }

    /**
     * Get templateContainer
     *
     * @return string
     */
    public function getTemplateContainer()
    {
        return $this->templateContainer;
    }

    /**
     * Set report
     *
     * @param \TechPromux\DynamicReportBundle\Entity\Report $report
     *
     * @return Component
     */
    public function setReport(\TechPromux\DynamicReportBundle\Entity\Report $report)
    {
        $this->report = $report;

        return $this;
    }

    /**
     * Get report
     *
     * @return \TechPromux\DynamicReportBundle\Entity\Report
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * Set datamodel
     *
     * @param \TechPromux\DynamicQueryBundle\Entity\DataModel $datamodel
     *
     * @return Component
     */
    public function setDatamodel(\TechPromux\DynamicQueryBundle\Entity\DataModel $datamodel)
    {
        $this->datamodel = $datamodel;

        return $this;
    }

    /**
     * Get datamodel
     *
     * @return \TechPromux\DynamicQueryBundle\Entity\DataModel
     */
    public function getDatamodel()
    {
        return $this->datamodel;
    }
}

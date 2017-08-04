<?php

namespace TechPromux\DynamicReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use TechPromux\BaseBundle\Entity\Context\BaseResourceContext;
use TechPromux\BaseBundle\Entity\Context\HasResourceContext;
use TechPromux\BaseBundle\Entity\Resource\BaseResource;

/**
 * Report
 *
 * @ORM\Table(name="techpromux_dynamic_report_report")
 * @ORM\Entity()
 */
class Report extends BaseResource implements HasResourceContext
{
    /**
     * @var string
     *
     * @ORM\Column(name="template_name", type="string", length=255)
     */
    private $templateName;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OrderBy({"position" = "ASC"})
     * @ORM\OneToMany(targetEntity="Component", mappedBy="report", cascade={"all"}, orphanRemoval=true)
     */
    private $components;

    /**
     * @var BaseResourceContext
     *
     * @ORM\ManyToOne(targetEntity="TechPromux\BaseBundle\Entity\Context\BaseResourceContext")
     * @ORM\JoinColumn(name="context_id", referencedColumnName="id", nullable=true)
     */
    protected $context;

    /**
     * Set templateName
     *
     * @param string $templateName
     *
     * @return Report
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;

        return $this;
    }

    /**
     * Get templateName
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    public function __toString()
    {
        return $this->getTitle() ? $this->getTitle() : '';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->components = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add component
     *
     * @param \TechPromux\DynamicReportBundle\Entity\Component $component
     *
     * @return Report
     */
    public function addComponent(\TechPromux\DynamicReportBundle\Entity\Component $component)
    {
        $this->components[] = $component;

        return $this;
    }

    /**
     * Remove component
     *
     * @param \TechPromux\DynamicReportBundle\Entity\Component $component
     */
    public function removeComponent(\TechPromux\DynamicReportBundle\Entity\Component $component)
    {
        $this->components->removeElement($component);
    }

    /**
     * Get components
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComponents()
    {
        return $this->components;
    }


    /**
     * Set owner
     *
     * @param BaseResourceContext $context
     *
     * @return DataSource
     */
    public function setContext(BaseResourceContext $context = null)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get owner
     *
     * @return BaseResourceContext
     */
    public function getContext()
    {
        return $this->context;
    }
}

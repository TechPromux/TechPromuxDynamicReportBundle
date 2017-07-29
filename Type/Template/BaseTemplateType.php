<?php

namespace  TechPromux\DynamicReportBundle\Type\Template;

interface BaseTemplateType {

    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getGroupName();

    /**
     * @return string
     */
    public function getRelativePath();

    /**
     * @return string
     */
    public function getAbsolutePath();

    /**
     * @return string
     */
    public function getFolderPath();

    /**
     * @return array
     */
    public function getContainersNames();

    /**
     * @return string
     */
    public function getImageDescriptionUrl();


}

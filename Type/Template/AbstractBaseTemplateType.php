<?php
/**
 * Created by PhpStorm.
 * User: franklin
 * Date: 13/07/2017
 * Time: 15:05
 */

namespace TechPromux\Bundle\DynamicReportBundle\Type\Template;


abstract class AbstractBaseTemplateType implements BaseTemplateType
{
    /**
     * @return string
     */
    public function getGroupName()
    {
        return 'techpromux.default';
    }

    /**
     * @return string
     */
    public function getAbsolutePath()
    {
        return $this->getFolderPath() . '/' . $this->getRelativePath();
    }

    /**
     * @return string
     */
    public function getFolderPath()
    {
        return 'TechPromuxDynamicReportBundle:Type:Template';
    }

}
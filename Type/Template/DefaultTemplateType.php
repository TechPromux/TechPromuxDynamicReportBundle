<?php
/**
 * Created by PhpStorm.
 * User: franklin
 * Date: 13/07/2017
 * Time: 15:19
 */

namespace TechPromux\Bundle\DynamicReportBundle\Type\Template;


class DefaultTemplateType extends AbstractBaseTemplateType
{

    /**
     * @return string
     */
    public function getId()
    {
        return 'techpromux.template.default';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'template.default';
    }

    /**
     * @return string
     */
    public function getRelativePath()
    {
        return 'default.html.twig';
    }

    /**
     * @return array
     */
    public function getContainersNames()
    {
        return array(
            'content_top' => 'content_top',
            'content' => 'content',
            'content_bottom' => 'content_bottom',
        );
    }

    /**
     * @return string
     */
    public function getImageDescriptionUrl()
    {
        return '/bundles/tecpromuxdynamicquerybundle/images/template/default.png';
    }
}
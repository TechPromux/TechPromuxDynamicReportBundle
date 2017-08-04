<?php
/**
 * Created by PhpStorm.
 * User: franklin
 * Date: 13/07/2017
 * Time: 15:19
 */

namespace TechPromux\DynamicReportBundle\Type\Template;


class TwoColumnsTemplateType extends AbstractBaseTemplateType
{

    /**
     * @return string
     */
    public function getId()
    {
        return 'techpromux.template.2columns';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'template.2columns';
    }

    /**
     * @return string
     */
    public function getRelativePath()
    {
        return '2columns.html.twig';
    }

    /**
     * @return array
     */
    public function getContainersNames()
    {
        return array(
            'content_top' => 'content_top',
            'content_left' => 'content_left',
            'content_right' => 'content_right',
            'content_bottom' => 'content_bottom',
        );
    }

    /**
     * @return string
     */
    public function getImageDescriptionUrl()
    {
        return '/bundles/tecpromuxdynamicquerybundle/images/template/2columns.png';
    }
}
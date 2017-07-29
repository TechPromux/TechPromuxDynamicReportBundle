<?php

namespace TechPromux\DynamicReportBundle\Type\Component;

use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use  TechPromux\DynamicQueryBundle\Entity\DataModelDetail;
use  TechPromux\DynamicQueryBundle\Type\ConditionalOperator\BaseConditionalOperatorType;
use  TechPromux\DynamicReportBundle\Entity\Component;
use  TechPromux\DynamicReportBundle\Manager\ComponentManager;
use  TechPromux\DynamicReportBundle\Manager\UtilDynamicReportManager;

abstract class AbstractDataModelComponentType extends AbstractComponentType
{

    /**
     * @return array
     */
    /**
     * @return array
     */
    public function getExportablesFormats()
    {
        return array(
            'xls' => 'xls',
            'csv' => 'csv',
            'json' => 'json',
        );
    }

    /**
     * @return array
     */
    public function getExportablesFormatsIconsClasses()
    {
        return array(
            'xls' => 'fa-file-excel-o',
            'csv' => 'fa-file-text-o',
            'json' => 'fa-file-code-o',
        );
    }
    //-----------------------------------------------------------------------------


    /**
     * @return array
     */
    public function getDefaultDataSettings(Component $component)
    {
        $default_settings = parent::getDefaultDataSettings($component);

        $default_settings['dataset_type'] = $this->getDataModelDatasetType();

        $details = $this->getUtilDynamicReportManager()->getComponentManager()->getDataModelDetailsChoices($component);

        $details_for_alls = $details['details'];
        //$details_for_labels_choices = $details['details_for_labels_choices'];
        //$details_for_series_choices = $details['details_for_series_choices'];
        $details_for_datas_choices = $details['details_for_datas_choices'];

        switch ($this->getDataModelDatasetType()) {
            case 'multiple':

                $default_settings['dataset_multiple_details_for_datas'] = array();

                foreach ($details_for_datas_choices as $dt) {
                    $default_settings['dataset_multiple_details_for_datas'][] = array(
                        'detail_id' => $dt['id'],
                        'detail_label' => 'title',
                        'text_align' => ($dt['classification'] == 'number' ? 'right' : ($dt['classification'] == 'datetime' ? 'center' : 'left')),
                        'text_with' => '',
                        'show_prefix' => true,
                        'show_suffix' => true,
                    );
                }

                break;

            case 'crossed':

                $default_settings['dataset_crossed_detail_for_labels'] = array(
                    'detail_id' => null,
                    'detail_label' => 'title',
                    'text_align' => 'left',
                    'text_with' => '',
                    'show_prefix' => true,
                    'show_suffix' => true,
                );
                $default_settings['dataset_crossed_detail_for_series'] = array(
                    'detail_id' => null,
                    'detail_label' => 'title',
                    'text_align' => 'center',
                    'text_with' => '',
                    'show_prefix' => true,
                    'show_suffix' => true,
                );
                $default_settings['dataset_crossed_detail_for_datas'] = array(
                    'detail_id' => null,
                    'detail_label' => 'title',
                    'text_align' => 'center',
                    'text_with' => '',
                    'show_prefix' => true,
                    'show_suffix' => true,
                    'summary_function' => null
                );

                break;
            case 'series_single':

                $default_settings['dataset_series_single_detail_for_label'] = array(
                    'detail_id' => null,
                    'detail_label' => 'title',
                    'text_align' => 'left',
                    'text_with' => '',
                    'show_prefix' => true,
                    'show_suffix' => true,
                );
                $default_settings['dataset_series_single_detail_for_data'] = array(
                    'detail_id' => null,
                    'detail_label' => 'title',
                    'text_align' => 'center',
                    'text_with' => '',
                    'show_prefix' => true,
                    'show_suffix' => true,
                );

            case 'series_multiple':

                $default_settings['dataset_series_multiple_detail_for_label'] = array(
                    'detail_id' => null,
                    'show_prefix' => true,
                    'show_suffix' => true,
                    'show_filter' => true,
                );

                $default_settings['dataset_series_multiple_details_for_data'] = array();

                foreach ($details_for_datas_choices as $dt) {
                    $default_settings['dataset_series_multiple_details_for_data'][] = array(
                        'detail_id' => $dt['id'],
                        'detail_label' => 'title',
                        'text_align' => ($dt['classification'] == 'number' ? 'right' : ($dt['classification'] == 'datetime' ? 'center' : 'left')),
                        'text_with' => '',
                        'show_prefix' => true,
                        'show_suffix' => true,
                    );
                }

                break;


                break;
        }

        $default_settings['dataset_filter_options'] = array();

        // TODO adicionar todos los filtros en caso de crossed or single??
        foreach ($details_for_alls as $dt) {
            $default_settings['dataset_filter_options'][] = array(
                'detail_id' => $dt['id'],
                'widget_type' => 'text',
            );
        }

        $default_settings['dataset_order_options'] = array();

        if ($this->getDataModelDatasetResultPaginated()) {
            $default_settings['dataset_paginator_options'] = array(
                'initial_page' => 1,
                'items_per_page' => 32,
                'max_paginator_links' => 5,
                'show_row_number' => true,
            );
        }

        return $default_settings;

    }

    /**
     * @return array
     */
    public function getDefaultDataSettingsForEditForm(Component $component)
    {
        $keys = array();

        $details = $this->getUtilDynamicReportManager()->getComponentManager()->getDataModelDetailsChoices($component);

        $details_for_alls = $details['details'];
        $details_for_labels_choices = $details['details_for_labels_choices'];
        $details_for_series_choices = $details['details_for_series_choices'];
        $details_for_datas_choices = $details['details_for_data_choices'];

        $default_settings['dataset_type'] = $this->getDataModelDatasetType();

        switch ($this->getDataModelDatasetType()) {
            case 'multiple':

                $keys[] = array('dataset_multiple_details_for_datas', 'sonata_type_native_collection', array(
                    'entry_type' => 'sonata_type_immutable_array',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_options' => array(
                        'keys' => array(
                            array('detail_id', 'choice', array(
                                "multiple" => false, "expanded" => false, "required" => true,
                                'choices' => $details_for_datas_choices, // TODO preguntar al component type si el data es numeric or date
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'),
                            )
                            ),
                            array('detail_label', 'choice', array(
                                'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                                "multiple" => false, "expanded" => false, "required" => true,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                            )),
                            array('text_align', 'choice', array(
                                "multiple" => false, "expanded" => false, "required" => true,
                                'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                ),
                            )
                            ),
                            array('text_with', 'text', array(
                                "required" => false,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                                'attr' => array(
                                    'placeholder' => 'px',
                                    //'style' => 'width: 70px;'
                                )
                            )),
                            array('show_prefix', 'checkbox', array(
                                'required' => false,
                                "label_attr" => array(
                                    'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                    'style' => 'width: 170px;max-width: 200%;'
                                ),
                            )),
                            array('show_suffix', 'checkbox', array(
                                'required' => false,
                                "label_attr" => array(
                                    'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                    'style' => 'width: 170px;max-width: 200%;'
                                ),
                            )),
                        )
                    )
                ));

                break;
            case 'crossed':

                $keys[] = array('dataset_crossed_detail_for_labels', 'sonata_type_immutable_array', array(
                    //'label' => false,
                    'keys' => array(
                        array('detail_id', 'choice', array(
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => $details_for_labels_choices, // TODO preguntar al component type si el data es numeric or date
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-3'),
                        )
                        ),
                        array('detail_label', 'choice', array(
                            'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                            "multiple" => false, "expanded" => false, "required" => true,
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                        )),
                        array('text_align', 'choice', array(
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                            ),
                        )
                        ),
                        array('text_with', 'text', array(
                            "required" => false,
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1'),
                            'attr' => array(
                                'placeholder' => 'px',
                            )
                        )),
                        array('show_prefix', 'checkbox', array(
                            //'label' => 'Prefix',
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                        array('show_suffix', 'checkbox', array(
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                    )
                ));

                $keys[] = array('dataset_crossed_detail_for_series', 'sonata_type_immutable_array', array(
                    'keys' => array(
                        array('detail_id', 'choice', array(
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => $details_for_series_choices, // TODO preguntar al component type si el data es numeric or date
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-3'),
                        )
                        ),
                        array('detail_label', 'choice', array(
                            'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                            "multiple" => false, "expanded" => false, "required" => true,
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                        )),
                        array('text_align', 'choice', array(
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                            ),
                        )
                        ),
                        array('text_with', 'text', array(
                            "required" => false,
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1'),
                            'attr' => array(
                                'placeholder' => 'px',
                                //'style' => 'width: 70px;'
                            )
                        )),
                        array('show_prefix', 'checkbox', array(
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                        array('show_suffix', 'checkbox', array(
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                    )
                ));

                $keys[] = array('dataset_crossed_detail_for_datas', 'sonata_type_immutable_array', array(
                    // 'label' => false,
                    'keys' => array(
                        array('detail_id', 'choice', array(
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => $details_for_datas_choices, // TODO preguntar al component type si el data es numeric or date
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-3'),
                        )
                        ),
                        array('detail_label', 'choice', array(
                            'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                            "multiple" => false, "expanded" => false, "required" => true,
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                        )),
                        array('text_align', 'choice', array(
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                            ),
                        )
                        ),
                        array('text_with', 'text', array(
                            "required" => false,
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1'),
                            'attr' => array(
                                'placeholder' => 'px',
                                //'style' => 'width: 70px;'
                            )
                        )),
                        array('summary_function', 'choice', array(
                            'label' => 'Add Summary Column',
                            "multiple" => false, "expanded" => false, "required" => false,
                            'choices' => array(
                                'SUM' => 'SUM',
                                'AVG' => 'AVG',
                                'COUNT' => 'COUNT',
                                'MIN' => 'MIN',
                                'MAX' => 'MAX',
                            ),
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                        )),
                        array('show_prefix', 'checkbox', array(
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                        array('show_suffix', 'checkbox', array(
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                    )
                ));

                break;
            case 'series_single':

                $keys[] = array('dataset_series_single_detail_for_label', 'sonata_type_immutable_array', array(
                    //'label' => false,
                    'keys' => array(
                        array('detail_id', 'choice', array(
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => $details_for_labels_choices, // TODO preguntar al component type si el data es numeric or date
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'),
                        )
                        ),
                        array('detail_label', 'choice', array(
                            'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                            "multiple" => false, "expanded" => false, "required" => true,
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                        )),
                        array('text_align', 'choice', array(
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                            ),
                        )
                        ),
                        array('text_with', 'text', array(
                            "required" => false,
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                            'attr' => array(
                                'placeholder' => 'px',
                                //'style' => 'width: 70px;'
                            )
                        )),
                        array('show_prefix', 'checkbox', array(
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                        array('show_suffix', 'checkbox', array(
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                    )
                ));

                $keys[] = array('dataset_series_single_detail_for_data', 'sonata_type_immutable_array', array(
                    // 'label' => false,
                    'keys' => array(
                        array('detail_id', 'choice', array(
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => $details_for_data_choices, // TODO preguntar al component type si el data es numeric or date
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'),
                        )
                        ),
                        array('detail_label', 'choice', array(
                            'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                            "multiple" => false, "expanded" => false, "required" => true,
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                        )),
                        array('text_align', 'choice', array(
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                            ),
                        )
                        ),
                        array('text_with', 'text', array(
                            "required" => false,
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                            'attr' => array(
                                'placeholder' => 'px',
                                //'style' => 'width: 70px;'
                            )
                        )),
                        array('show_prefix', 'checkbox', array(
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                        array('show_suffix', 'checkbox', array(
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                    )
                ));

                break;
            case 'series_multiple':

                $keys[] = array('dataset_series_multiple_detail_for_label', 'sonata_type_immutable_array', array(
                    //'label' => false,
                    'keys' => array(
                        array('detail_id', 'choice', array(
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => $details_for_labels_choices, // TODO preguntar al component type si el data es numeric or date
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'),
                        )
                        ),
                        array('detail_label', 'choice', array(
                            'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                            "multiple" => false, "expanded" => false, "required" => true,
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                        )),
                        array('text_align', 'choice', array(
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                            ),
                        )
                        ),
                        array('text_with', 'text', array(
                            "required" => false,
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                            'attr' => array(
                                'placeholder' => 'px',
                                //'style' => 'width: 70px;'
                            )
                        )),
                        array('show_prefix', 'checkbox', array(
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                        array('show_suffix', 'checkbox', array(
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                        )),
                    )
                ));

                $keys[] = array('dataset_series_multiple_details_for_data', 'sonata_type_native_collection', array(
                    'entry_type' => 'sonata_type_immutable_array',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_options' => array(
                        'keys' => array(
                            array('detail_id', 'choice', array(
                                "multiple" => false, "expanded" => false, "required" => true,
                                'choices' => $details_for_data_choices, // TODO preguntar al component type si el data es numeric or date
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'),
                            )
                            ),
                            array('detail_label', 'choice', array(
                                'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                                "multiple" => false, "expanded" => false, "required" => true,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                            )),
                            array('text_align', 'choice', array(
                                "multiple" => false, "expanded" => false, "required" => true,
                                'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                ),
                            )
                            ),
                            array('text_with', 'text', array(
                                "required" => false,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                                'attr' => array(
                                    'placeholder' => 'px',
                                    //'style' => 'width: 70px;'
                                )
                            )),
                            array('show_prefix', 'checkbox', array(
                                'required' => false,
                                "label_attr" => array(
                                    'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                    'style' => 'width: 170px;max-width: 200%;'
                                ),
                            )),
                            array('show_suffix', 'checkbox', array(
                                'required' => false,
                                "label_attr" => array(
                                    'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                    'style' => 'width: 170px;max-width: 200%;'
                                ),
                            )),
                        )
                    )
                ));
        }

        $keys[] = array('dataset_filter_options', 'sonata_type_native_collection', array(
            'entry_type' => 'sonata_type_immutable_array',
            'allow_add' => true,
            'allow_delete' => true,
            'entry_options' => array(
                'keys' => array(
                    array('detail_id', 'choice', array(
                        "multiple" => false, "expanded" => false, "required" => true,
                        'choices' => $details_for_labels_choices,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-7'),
                    )),
                    array('widget_type', 'choice', array(
                        "multiple" => false, "expanded" => false, "required" => true,
                        'choices' => array(
                            'text' => 'text',
                            'boolean' => 'boolean',
                            'datetime' => 'datetime',
                            'choices' => 'choices'
                        ),
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-3'),
                    )),

                ),
            )
        ));

        $keys[] = array('dataset_order_options', 'sonata_type_native_collection', array(
            'entry_type' => 'sonata_type_immutable_array',
            'allow_add' => true,
            'allow_delete' => true,
            'entry_options' => array(
                'keys' => array(
                    array('detail_id', 'choice', array(
                        "multiple" => false, "expanded" => false, "required" => true,
                        'choices' => $details_for_labels_choices,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-7'),
                    )
                    ),
                    array('order_type', 'choice', array(
                        "multiple" => false, "expanded" => false, "required" => true,
                        'choices' => array('asc' => 'asc', 'desc' => 'desc'), // TODO add translator
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-3'),
                    )
                    ),
                )
            )
        ));

        if ($this->getDataModelDatasetResultPaginated()) {
            $keys[] = array('dataset_paginator_options', 'sonata_type_immutable_array', array(
                'keys' => array(
                    array('initial_page', 'number', array(
                        "label_attr" => array(
                            //'class' => 'pull-left',
                            'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'
                        ),
                    )),
                    array('items_per_page', 'number', array(
                        "label_attr" => array(
                            // 'class' => 'pull-left',
                            'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-3'
                        ),
                    )),
                    array('max_paginator_links', 'number', array(
                        "label_attr" => array(
                            //'class' => 'pull-left',
                            'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-3'
                        ),
                    )),
                    array('show_row_number', 'checkbox', array(
                        'required' => false,
                        "label_attr" => array(
                            //'class' => 'pull-left',
                            //'style' => 'margin-right:5px;',
                            'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'
                        ),
                    )),
                )
            ));
        }
        $parent_keys = parent::getDefaultDataSettingsForEditForm($component);

        foreach ($parent_keys as $key) {
            $keys[] = $key;
        }

        return $keys;

    }


    //-----------------------------------------------------------------------------

    /**
     * @param Request|null $request
     * @param FormBuilderInterface $filters_form_builder
     * @param Component $component
     * @param array $parameters
     *
     * @return \FormBuilderInterface
     */
    public function configureFiltersFormBuilder(Request $request = null, $filters_form_builder, Component $component, array $parameters = array())
    {
        // TODO esto va en el otro abstract type (que lo llama desde le manager)?

        $dataOptions = $component->getDataOptions();

        $dataset_filter_options = $dataOptions['dataset_filter_options'];

        $datamodel_details_descriptions = $this->getUtilDynamicReportManager()
            ->getComponentManager()
            ->getDataModelManager()
            ->getEnabledDetailsDescriptionsFromDataModel($component->getDatamodel());

        $i = 0;
        foreach ($dataset_filter_options as $filter) {

            $detail = $datamodel_details_descriptions[$filter['detail_id']];

            $operators_for_detail_type = $this->getUtilDynamicReportManager()
                ->getComponentManager()
                ->getDataModelManager()
                ->getUtilDynamicQueryManager()
                ->getConditionalOperatorsChoices($detail['type']);


            $filters_form_builder->add("id_" . $i, 'hidden', array(
                'label' => $detail['title'],
                'required' => false,
                'empty_data' => $detail['id'],
                'attr' => array('value' => $detail['id'])
            ));
            $filters_form_builder->add("operator_" . $i, 'choice', array(
                'label' => false,
                'required' => false,
                'choices' => $operators_for_detail_type,
                'multiple' => false,
                'expanded' => false,
                'translation_domain' => $this->getUtilDynamicReportManager()
                    ->getComponentManager()
                    ->getDataModelManager()
                    ->getUtilDynamicQueryManager()->getBundleName()
            ));

            if ($filter['widget_type'] == 'choices') {

                $detail_for_choices = $this->getUtilDynamicReportManager()
                    ->getComponentManager()
                    ->getDataModelManager()->getDatamodelDetailManager()->find($detail['id']);

                $filter_values_choices = $this->getUtilDynamicReportManager()
                    ->getComponentManager()
                    ->getDataModelManager()->getDistinctResultsChoicesFromDatamodelDetail($detail_for_choices);

                $filters_form_builder->add("value_" . $i, 'choice', array(
                    'label' => false,
                    'required' => false,
                    'choices' => $filter_values_choices,
                ));
            } elseif ($filter['widget_type'] == 'boolean' || in_array($detail['type'], array('boolean'))) {
                $filters_form_builder->add("value_" . $i, 'choice', array(
                    'label' => false,
                    'required' => false,
                    'choices' => array(
                        'false' => 0,
                        'true' => 1,
                    ),
                ));
            } elseif ($filter['widget_type'] == 'datetime' || in_array($detail['type'], array('date', 'time', 'datetime'))) {
                $filters_form_builder->add("value_" . $i, $detail['type'], array(
                    'label' => false,
                    'required' => false,
                    'widget' => 'single_text',
                    'attr' => array('class' => $detail['type'], 'type' => $detail['type'])
                ));
            } else {
                $filters_form_builder->add("value_" . $i, 'text', array(
                    'label' => false,
                    'required' => false,
                ));
            }
            $i++;
        }

        $filters_form_builder->add("_filters_count", 'hidden', array(
            'label' => false,
            'required' => false,
            'empty_data' => $i,
            'attr' => array('value' => $i)
        ));

        // TODO solo si necesita paginacion?

        $filters_form_builder->add("_page", 'hidden', array(
            'label' => false,
            'required' => false,
            'empty_data' => 1,
        ));
        $filters_form_builder->add("_items_per_page", 'hidden', array(
            'label' => false,
            'required' => false,
            'empty_data' => 32,
        ));

        $filters_form_builder->add("_sort_by", 'hidden', array(
            'label' => false,
            'required' => false,
            'empty_data' => '',
            'attr' => array('value' => '')
        ));
        $filters_form_builder->add("_sort_type", 'hidden', array(
            'label' => false,
            'required' => false,
            'empty_data' => '',
            'attr' => array('value' => '')
        ));

        return $filters_form_builder;
    }

    /**
     * @param Request $request
     * @param array $filter_form_data
     * @param Component $component
     * @param array $parameters
     *
     * @return array
     * @throws \Exception
     */
    public function createFiltersByDescriptions(Request $request, array $filter_form_data, Component $component, array $parameters)
    {

        $dataOptions = $component->getDataOptions();

        $dataset_filter_options = $dataOptions['dataset_filter_options'];

        $datamodel_details_descriptions = $this->getUtilDynamicReportManager()
            ->getComponentManager()
            ->getDataModelManager()
            ->getEnabledDetailsDescriptionsFromDataModel($component->getDatamodel());

        $operators = $this->getUtilDynamicReportManager()->getComponentManager()
            ->getDatamodelManager()->getUtilDynamicQueryManager()->getRegisteredConditionalOperators();

        $filters_by = array();

        $i = 0;
        foreach ($dataset_filter_options as $filter) {
            if (isset($filter_form_data['id_' . $i])) {
                $detail_id = $filter_form_data['id_' . $i];

                $operator_id = $filter_form_data['operator_' . $i];

                $value = $filter_form_data['value_' . $i];

                $detail_description = $datamodel_details_descriptions[$detail_id];

                if (isset($operators[$operator_id])) {
                    $operator = $operators[$operator_id];
                    /* @var $operator BaseConditionalOperatorType */

                    if ($operator->getIsUnary() || (!$operator->getIsUnary() && (!empty($value) || $value == 0 || $value == '0'))) {
                        if ($operator->getIsUnary()) {
                            $value = null;
                        } else {
                            if ($detail_description['type'] == 'date') {
                                $value = $value->format('Y-m-d') ? $value->format('Y-m-d') : $value->format('Y-m-d 00:00:00');
                            } else if ($detail_description['type'] == 'time') {
                                $value = $value->format('H:i:s') ? $value->format('H:i:s') : $value->format('Y-m-d H:i:s');
                            } else if ($detail_description['type'] == 'datetime') {
                                $value = $value->format('Y-m-d H:i:s');
                            }
                        }
                        $filters_by[] = array(
                            'detail_id' => $detail_id,
                            'operator_id' => $operator->getId(),
                            'value' => $value,
                        );
                    }
                }
            }
            $i++;
        }

        return $filters_by;
    }

    /**
     * @param Request $request
     * @param array $filter_form_data
     * @param Component $component
     * @param array $parameters
     *
     * @return array
     * @throws \Exception
     */
    public function createOrdersByDescriptions(Request $request, array $filter_form_data, Component $component, array $parameters)
    {
        $orders_by = array();

        if (!empty($filter_form_data['_sort_by']) && !empty($filter_form_data['_sort_type'])) {
            $orders_by[] = array(
                'detail_id' => $filter_form_data['_sort_by'],
                'order_type' => $filter_form_data['_sort_type']
            );
        }

        $dataOptions = $component->getDataOptions();

        $dataset_order_options = $dataOptions['dataset_order_options'];

        foreach ($dataset_order_options as $od) {
            $_sort_by = isset($filter_form_data['_sort_by']) ? $filter_form_data['_sort_by'] : null;
            if ($od['detail_id'] != $_sort_by) {
                $orders_by[] = array(
                    'detail_id' => $od['detail_id'],
                    'order_type' => $od['order_type']
                );
            }
        }

        return $orders_by;
    }

    //------------------------------------------------------------------------------------------------------------

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $parameters
     * @param bool $full_exportable_data
     * @return array
     * @throws \Exception
     */
    public function getDataFromComponentExecution(Request $request = null, Component $component, array $parameters = array(), $full_exportable_data = false)
    {
        $filters_form = $this->createFiltersForm($request, $component, $parameters);

        $filters_form->handleRequest($request); // or handleRequest?

        if (count($filters_form->getErrors()) > 0 && !$filters_form->isValid()) { // if ($request->isXmlHttpRequest())
            throw new \Exception("ERROR!!!. Filter data isn´t valid");
        }

        $filter_form_data = $filters_form->getData();

        //-------------------------------------------

        $datamodel = $component->getDatamodel();

        $queryBuilder = $this->getUtilDynamicReportManager()->getComponentManager()->getDatamodelManager()->getQueryBuilderFromDataModel($datamodel);

        //--------------------------------------------

        // aplicar filtros al queryBuilder

        $filters_by = $this->createFiltersByDescriptions($request, $filter_form_data, $component, $parameters);

        $datamodel_details_descriptions = $this->getUtilDynamicReportManager()
            ->getComponentManager()->getDataModelManager()
            ->getEnabledDetailsDescriptionsFromDataModel($component->getDatamodel());

        $operators = $this->getUtilDynamicReportManager()->getComponentManager()
            ->getDatamodelManager()->getUtilDynamicQueryManager()->getRegisteredConditionalOperators();

        foreach ($filters_by as $filter_by_description) {
            $detail_id = $filter_by_description['detail_id'];
            $detail = $datamodel_details_descriptions[$detail_id];
            $operator_id = $filter_by_description['operator_id'];
            $operator = $operators[$operator_id];
            /* @var $operator BaseConditionalOperatorType */
            $value = $filter_by_description['value'];

            $param_name = null;

            if ($operator->getIsForRightOperandAsArray()) {
                $param_name = array();
                foreach (explode(',', $value) as $v) {
                    $param_name[] = $queryBuilder->createNamedParameter(trim($v));
                }
            } else {
                $param_name = $queryBuilder->createNamedParameter($value);
            }
            $cdn_stm = $operator->getConditionalStatement($detail['alias'], $param_name);

            $queryBuilder->andHaving($cdn_stm);

        }

        //------------------------------------------------------------------------------------

        // aplicar orders al querybuilder

        $orders_by = $this->createOrdersByDescriptions($request, $filter_form_data, $component, $parameters);

        $originalOrders = $queryBuilder->getQueryPart('orderBy');
        $queryBuilder->resetQueryPart('orderBy');
        foreach ($orders_by as $order_by_description) {
            $detail_id = $order_by_description['detail_id'];
            $detail = $datamodel_details_descriptions[$detail_id];
            $order_type = $order_by_description['order_type'];
            $queryBuilder->addOrderBy($detail['alias'], $order_type);
        }
        foreach ($originalOrders as $or) {
            $parts = explode(' ', $or);
            $queryBuilder->addOrderBy($parts[0], $parts[1]);
        }

        //-----------------------------------------------------------------------------------------------------

        $data = array();

        if ($this->getDataModelDatasetResultPaginated() && !$full_exportable_data) {

            $dataOptions = $component->getDataOptions();

            $dataset_paginator_options = $dataOptions['dataset_paginator_options'];


            $paginator = $this->getUtilDynamicReportManager()->getComponentManager()->getDatamodelManager()->createPaginatorForQueryBuilder($queryBuilder);

            $page = $request->get('_page', $dataset_paginator_options['initial_page']); // ver como obtener la pagina por defecto de la configuracion (obtenerlo del filter data)
            $items_per_page = $request->get('_items_per_page', $dataset_paginator_options['items_per_page']); // obtener la cantidad por defecto de la configuracion

            $paginator->setMaxPerPage($items_per_page);
            $rowCount = $queryBuilder->execute()->rowCount();

            //$paginator->setAllowOutOfRangePages(false);

            if (intval($page) * $paginator->getMaxPerPage() <= $rowCount || ((intval($page) - 1) * $paginator->getMaxPerPage() < $rowCount)) {
                $paginator->setCurrentPage($page);
            } else {
                $paginator->setCurrentPage(1);
            }

            $currentPageResults = $paginator->getCurrentPageResults();

            $data = array(
                'paginator' => $paginator,
                'result' => $currentPageResults
            );
        } else {

            $result = $queryBuilder->execute()->fetchAll();

            $data = array(
                'result' => $result
            );
        }

        return $data;

    }

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $parameters
     * @param bool $full_exportable_data
     * @return array
     */
    protected function getMergedDataParametersAndSettings(Request $request = null, Component $component, array $parameters = array(), $full_exportable_data = false)
    {
        $all = parent::getMergedDataParametersAndSettings($request, $component, $parameters, $full_exportable_data); // TODO: Change the autogenerated stub

        //-----------------------------

        $labelsDescriptions = $this->getUtilDynamicReportManager()
            ->getComponentManager()
            ->getDataModelManager()
            ->getEnabledDetailsDescriptionsFromDataModel($component->getDatamodel());

        $datamodelDetails = array();
        $datamodelDetailsDescriptions = array();

        switch ($this->getDataModelDatasetType()) {
            case 'multiple':
                $details_for_datas = $all['settings']['dataset_multiple_details_for_datas'];
                $datamodelDetails = $details_for_datas;
                break;
            case 'crossed':
                $detail_for_labels = $all['settings']['dataset_crossed_detail_for_labels'];
                $detail_for_series = $all['settings']['dataset_crossed_detail_for_series'];
                $detail_for_datas = $all['settings']['dataset_crossed_detail_for_datas'];
                $datamodelDetails = array($detail_for_labels, $detail_for_series, $detail_for_datas);

                $all['data']['_labels'] = array();
                $all['data']['_series'] = array();
                $all['data']['_datas'] = array();

                $_crossed_labels = array();
                $_crossed_series = array();
                $_crossed_datas = array();

                foreach ($all['data']['result'] as $row) {

                    $col_label = $row[$labelsDescriptions[$detail_for_labels['detail_id']]['alias']];
                    $l_format = $labelsDescriptions[$detail_for_labels['detail_id']]['format'];
                    $l_prefix = $detail_for_labels['show_prefix'] ? $labelsDescriptions[$detail_for_labels['detail_id']]['prefix'] : '';
                    $l_suffix = $detail_for_labels['show_suffix'] ? $labelsDescriptions[$detail_for_labels['detail_id']]['suffix'] : '';
                    $col_label_formatted = $l_prefix . $this->getUtilDynamicReportManager()
                            ->getComponentManager()
                            ->getDataModelManager()->getUtilDynamicQueryManager()->formatValue($col_label, $l_format)
                        . $l_suffix;
                    $_crossed_labels[$col_label_formatted] = $col_label_formatted;

                    $col_series = $row[$labelsDescriptions[$detail_for_series['detail_id']]['alias']];
                    $s_format = $labelsDescriptions[$detail_for_series['detail_id']]['format'];
                    $s_prefix = $detail_for_series['show_prefix'] ? $labelsDescriptions[$detail_for_series['detail_id']]['prefix'] : '';
                    $s_suffix = $detail_for_series['show_suffix'] ? $labelsDescriptions[$detail_for_series['detail_id']]['suffix'] : '';
                    $col_series_formatted = $s_prefix . $this->getUtilDynamicReportManager()
                            ->getComponentManager()
                            ->getDataModelManager()->getUtilDynamicQueryManager()->formatValue($col_series, $s_format)
                        . $s_suffix;
                    $_crossed_series[$col_series_formatted] = $col_series_formatted;

                    if (!isset($_crossed_datas[$col_series_formatted])) {
                        $_crossed_datas[$col_series_formatted] = array();
                    }

                    $col_datas = $row[$labelsDescriptions[$detail_for_datas['detail_id']]['alias']];
                    $_crossed_datas[$col_series_formatted][$col_label_formatted] = $col_datas;
                }

                foreach ($_crossed_labels as $label) {
                    $all['data']['_labels'][] = $label;
                }

                foreach (array_keys($_crossed_datas) as $serie) {
                    $all['data']['_series'][$serie] = array(
                        'title' => $serie,
                        'type' => $labelsDescriptions[$detail_for_datas['detail_id']]['type'],
                        'format' => $labelsDescriptions[$detail_for_datas['detail_id']]['format'],
                        'classification' => $labelsDescriptions[$detail_for_datas['detail_id']]['classification'],
                        'prefix' => $detail_for_datas['show_prefix'] ? $labelsDescriptions[$detail_for_datas['detail_id']]['prefix'] : '',
                        'suffix' => $detail_for_datas['show_prefix'] ? $labelsDescriptions[$detail_for_datas['detail_id']]['suffix'] : '',
                    );
                    $all['data']['_datas'][$serie] = array();
                    foreach ($_crossed_labels as $label) {
                        $data_value = isset($_crossed_datas[$serie][$label]) ? $_crossed_datas[$serie][$label] : null;
                        $all['data']['_datas'][$serie][] = $data_value;
                    }
                }

                break;
            case 'series_single':

                break;
            case 'series_multiple':

                break;
        }

        foreach ($datamodelDetails as $detail) {
            $id = $detail['detail_id'];
            $datamodelDetailsDescriptions[$id] = array(
                'id' => $id,
                'alias' => $labelsDescriptions[$id]['alias'],
                'title' => $detail['detail_label'] == 'title' ? $labelsDescriptions[$id]['title'] : $labelsDescriptions[$id]['abbreviation'],
                'prefix' => $detail['show_prefix'] && !is_null($labelsDescriptions[$id]['prefix']) ? $labelsDescriptions[$id]['prefix'] : '',
                'suffix' => $detail['show_suffix'] && !is_null($labelsDescriptions[$id]['suffix']) ? $labelsDescriptions[$id]['suffix'] : '',
                'type' => $labelsDescriptions[$id]['type'],
                'format' => $labelsDescriptions[$id]['format'],
                'classification' => $labelsDescriptions[$id]['classification'],
                'text_align' => $detail['text_align'],
                'text_with' => $detail['text_with'],
            );
        }

        //$all['extras']['datamodel_details_labels_keys'] = array_keys($datamodelDetailsDescriptions);
        $all['extras']['datamodel_details_descriptions'] = $datamodelDetailsDescriptions;

        //dump($all);

        return $all;
    }

    //------------------------------------------------------------------------------------------------

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function createExportableData(Request $request = null, Component $component, array $parameters = array())
    {
        $all = $this->getMergedDataParametersAndSettings($request, $component, $parameters, true);

        //----------------------------------------------------------

        $exportableLabels = array();
        $exportableSeries = array();
        $exportableValues = array();
        $exportableDescriptionType = '';
        $exportableFieldsDescriptions = array();

        //----------------------------------------------------------

        $has_series = false; // TODO averiguar si tiene o no tiene serie

        $show_row_number = false;

        $formatter_helper = $all['extras']['formatter_helper'];

        $labelsDescriptions = $all['extras']['datamodel_details_descriptions']; // TODO

        switch ($this->getDataModelDatasetType()) {
            case 'multiple':
                $exportableDescriptionType = 'labels';

                $exportableFieldsDescriptions = array_values($labelsDescriptions);

                $data_result = $all['data']['result'];

                $labelsDescriptionsAlias = array(); // TODO
                foreach (array_values($labelsDescriptions) as $i => $lb) {
                    $alias = $lb['alias'];
                    $labelsDescriptionsAlias[$alias] = $i;
                    $exportableLabels[] = $lb['title'];
                }

                foreach ($data_result as $i => $row) {

                    $serie_id = $i;
                    $serie_data = array();

                    foreach ($row as $column => $value) {

                        $alias = $column;

                        $format = $exportableFieldsDescriptions[$labelsDescriptionsAlias[$alias]]['format'];
                        //$prefix = $exportableLabels[$alias]['prefix'];
                        //$suffix = $exportableLabels[$alias]['suffix'];
                        $txt = $formatter_helper->formatValue($value, $format);

                        $serie_data[] = htmlentities($txt);
                    }

                    $exportableValues[$serie_id] = $serie_data;
                }
                break;
            case 'crossed':
                $exportableDescriptionType = 'labels';


                break;
            case 'series_single':

                break;
            case 'series_multiple':
                $exportableDescriptionType = 'series';


                break;
        }

        $exportableData = array(
            'title' => $component->getTitle(),
            'has_series' => $has_series,
            'show_row_number' => $show_row_number,
            'descriptions_type' => $exportableDescriptionType,
            'descriptions' => $exportableFieldsDescriptions,
            'labels' => $exportableLabels,
            'series' => $exportableSeries,
            'datas' => $exportableValues,
        );

        return $exportableData;
    }

    /**
     * @param Request $request
     * @param Component $component
     * @param array $exportableData
     * @param $format
     * @return mixed
     */
    public function createExportableContent(Request $request, Component $component, array $exportableData = array(), $format)
    {
        $content = "";
        $contentType = "";

        $title = $exportableData['title'];

        $has_series = $exportableData['has_series'];

        $descriptionsType = $exportableData['descriptions_type'];

        $descriptions = $exportableData['descriptions'];

        $descriptionsKeys = array_keys($descriptions);

        $labels = $exportableData['labels'];

        $series = $exportableData['series'];

        $datas = $exportableData['datas'];

        switch ($format) {
            case 'xls':
                $contentType = 'application/vnd.ms-excel';

                $content = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name=ProgId content="Excel.Sheet"><meta name=Generator content=""></head><body>';
                $content .= '<table border="1">';
                $content .= '<tr><td colspan="' . (count($labels) + ($has_series ? 1 : 0)) . '">' . htmlentities($title) . '</td></tr>';
                $content .= '<tr>' . ($has_series ? '<td></td>' : '');
                foreach ($labels as $label) {
                    $content .= '<td>' . htmlentities($label) . '</td>';
                }
                $content .= '</tr>';

                foreach ($datas as $i => $row) {
                    $content .= '<tr>' . ($has_series ? '<td>' . htmlentities($i) . '</td>' : '');
                    foreach ($row as $col => $value) {
                        if ($descriptionsType=='labels')
                        {
                            $idx = $col;
                        }
                        else{ // series
                            $idx = $i;
                        }
                        $txt = $descriptions[$descriptionsKeys[$idx]]['prefix'] . $value . $descriptions[$descriptionsKeys[$idx]]['suffix'];
                        $content .= '<td style="text-align: ' . $descriptions[$descriptionsKeys[$col]]['text_align'] . '">' . htmlentities($txt) . '</td>';
                    }
                    $content .= '</tr>';
                }
                $content .= "</table>";
                $content .= "</body></html>";
                break;
            case 'csv':
                $contentType = 'text/csv';
                $content = '"' . str_replace('"', '\"', $title) . '"' . "\r\n";
                foreach ($labels as $i => $label) {
                    $content .= '"' . str_replace('"', '\"', $label) . '"' . (($i < count($labels) - 1) ? ';' : "\r\n");
                }
                foreach ($datas as $i => $row) {
                    //$content .= '"' . str_replace('"', '\"', $i) . '";';
                    foreach ($row as $col => $value) {
                        if ($descriptionsType=='labels')
                        {
                            $idx = $col;
                        }
                        else{ // series
                            $idx = $i;
                        }
                        $txt = $descriptions[$descriptionsKeys[$idx]]['prefix'] . $value . $descriptions[$descriptionsKeys[$idx]]['suffix'];
                        $content .= '"' . str_replace('"', '\"', $txt) . '"' . (($col < count($row) - 1) ? ';' : '');
                    }
                    $content .= (($i < count($datas) - 1) ? "\r\n" : '');
                }
                break;
            case 'json':
                $contentType = 'application/json';

                $descriptions_to_json = array();
                foreach ($descriptions as $i => $description) {
                    $descriptions_to_json[] = array(
                        'title' => $description['title'],
                        'prefix' => $description['prefix'],
                        'suffix' => $description['suffix'],
                        'type' => $description['type'],
                        'classification' => $description['classification'],
                        'text_align' => $description['text_align'],
                        'text_with' => $description['text_with'],
                    );
                }

                $labels_to_json = $labels;

                $series_to_json = array();
                foreach ($series as $i => $s) {
                    $series_to_json[] = array(
                        'title' => $s['title'],
                    );
                }

                $datas_to_json = array();
                foreach ($datas as $i => $d) {
                    $datas_to_json[] = $d;
                }
                $content = json_encode(
                    array(
                        'title' => $title,
                        'descriptions_type' => $descriptionsType,
                        'descriptions' => $descriptions_to_json,
                        'labels' => $labels_to_json,
                        'series' => $series_to_json,
                        'datas' => $datas_to_json,
                    ), JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION | JSON_BIGINT_AS_STRING | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                );
                break;
            default:
                throw new \RuntimeException('Invalid format');
        }

        $filename = strtolower(str_replace(' ', '_', $component->getTitle())) . '_' . date('Y_m_d_H_i_s', strtotime('now')) . '.' . $format;

        return new Response($content, 200, array(
            'Content-Type' => $contentType,
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
        ));
    }

    //------------------------------------------------------------------------------------------------------


}

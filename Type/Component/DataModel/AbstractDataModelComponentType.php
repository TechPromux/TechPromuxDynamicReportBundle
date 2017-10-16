<?php

namespace TechPromux\DynamicReportBundle\Type\Component\DataModel;

use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TechPromux\BaseBundle\Adapter\Paginator\DoctrineDbalPaginatorAdapter;
use TechPromux\DynamicQueryBundle\Type\ConditionalOperator\BaseConditionalOperatorType;
use TechPromux\DynamicReportBundle\Entity\Component;
use TechPromux\DynamicReportBundle\Type\Component\AbstractComponentType;

abstract class AbstractDataModelComponentType extends AbstractComponentType
{

    /**
     * @return boolean
     */
    abstract public function getHasDataModelDatasetLabel();

    /**
     * @return boolean
     */
    abstract public function getHasDataModelDatasetSeries();

    /**
     * @return boolean
     */
    abstract public function getHasDataModelDatasetMultipleDatas();

    /**
     * 'all', 'number', 'datetime', 'number_datetime'
     *
     * @return string
     */
    abstract public function getSupportedDataTypeFromDataModelDetails();

    /**
     * @return bool
     */
    public function getDataModelDatasetResultPaginated()
    {
        return false;
    }

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
        );
    }
    //-----------------------------------------------------------------------------

    /**
     * @return array
     */
    public function getDefaultDataSettings(Component $component)
    {
        $default_settings = parent::getDefaultDataSettings($component);

        $details = $this->getUtilDynamicReportManager()->getComponentManager()->getDataModelDetailsChoices($component);

        $details_for_alls = $details['details'];
        //$details_for_labels_choices = $details['details_for_labels_choices'];
        //$details_for_series_choices = $details['details_for_series_choices'];
        $details_for_datas_choices = $details['details_for_datas_choices'];

        if ($this->getHasDataModelDatasetLabel()) {
            $default_settings['dataset_detail_for_labels'] = array(
                'detail_id' => null,
                'detail_label' => 'title',
                'text_align' => 'left',
                'text_with' => '',
                'show_prefix' => true,
                'show_suffix' => true,
            );
        }
        if ($this->getHasDataModelDatasetSeries()) {
            $default_settings['dataset_detail_for_series'] = array(
                'detail_id' => null,
                'detail_label' => 'title',
                'text_align' => 'center',
                'text_with' => '',
                'show_prefix' => true,
                'show_suffix' => true,
            );
        }

        if (!$this->getHasDataModelDatasetMultipleDatas()) {
            $default_settings['dataset_detail_for_datas'] = array(
                'detail_id' => null,
                'detail_label' => 'title',
                'crossed_function' => null,
                'text_align' => 'center',
                'text_with' => '',
                'show_prefix' => true,
                'show_suffix' => true,
            );
        }
        if ($this->getHasDataModelDatasetMultipleDatas()) {
            $default_settings['dataset_details_for_datas'] = array();
            foreach ($details_for_datas_choices as $dt) {

                $default_settings['dataset_details_for_datas'][] = array(
                    'detail_id' => $details_for_alls[$dt]['id'],
                    'detail_label' => 'title',
                    'text_align' => ($details_for_alls[$dt]['classification'] == 'number' ? 'right' : ($details_for_alls[$dt]['classification'] == 'datetime' ? 'center' : 'left')),
                    'text_with' => '',
                    'show_prefix' => true,
                    'show_suffix' => true,
                );
            }
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

        //$details_for_alls = $details['details'];
        $details_for_labels_choices = $details['details_for_labels_choices'];
        $details_for_series_choices = $details['details_for_series_choices'];
        $details_for_datas_choices = $details['details_for_datas_choices'];

        if ($this->getHasDataModelDatasetLabel()) {
            $keys[] = array('dataset_detail_for_labels', 'sonata_type_immutable_array', array(
                //'label' => false,
                'keys' => array(
                    array('detail_id', 'choice', array(
                        "multiple" => false, "expanded" => false, "required" => true,
                        'choices' => $details_for_labels_choices, // TODO preguntar al component type si el data es numeric or date
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'),
                        //'translation_domain' => $this->getBundleName()
                    )
                    ),
                    array('detail_label', 'choice', array(
                        'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                        "multiple" => false, "expanded" => false, "required" => true,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                        //'translation_domain' => $this->getBundleName()
                    )),
                    array('text_align', 'choice', array(
                        "multiple" => false, "expanded" => false, "required" => true,
                        'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                        ),
                        //'translation_domain' => $this->getBundleName()
                    )
                    ),
                    array('text_with', 'text', array(
                        "required" => false,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                        'attr' => array(
                            'placeholder' => 'px',
                            //'style' => 'width: 70px;'
                        ),
                        //'translation_domain' => $this->getBundleName()
                    )),
                    array('show_prefix', 'checkbox', array(
                        'required' => false,
                        "label_attr" => array(
                            'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                            'style' => 'width: 170px;max-width: 200%;'
                        ),
                        //'translation_domain' => $this->getBundleName()
                    )),
                    array('show_suffix', 'checkbox', array(
                        'required' => false,
                        "label_attr" => array(
                            'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                            'style' => 'width: 170px;max-width: 200%;'
                        ),
                        //'translation_domain' => $this->getBundleName()
                    )),
                )
            ));
        }
        if ($this->getHasDataModelDatasetSeries()) {
            $keys[] = array('dataset_detail_for_series', 'sonata_type_immutable_array', array(
                'keys' => array(
                    array('detail_id', 'choice', array(
                        "multiple" => false, "expanded" => false, "required" => true,
                        'choices' => $details_for_series_choices, // TODO preguntar al component type si el data es numeric or date
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'),
                        //'translation_domain' => $this->getBundleName()
                    )
                    ),
                    array('detail_label', 'choice', array(
                        'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                        "multiple" => false, "expanded" => false, "required" => true,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                        //'translation_domain' => $this->getBundleName()
                    )),
                    array('text_align', 'choice', array(
                        "multiple" => false, "expanded" => false, "required" => true,
                        'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                        ),
                        //'translation_domain' => $this->getBundleName()
                    )
                    ),
                    array('text_with', 'text', array(
                        "required" => false,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                        'attr' => array(
                            'placeholder' => 'px',
                            //'style' => 'width: 70px;'
                        ),
                        //'translation_domain' => $this->getBundleName()
                    )),
                    array('show_prefix', 'checkbox', array(
                        'required' => false,
                        "label_attr" => array(
                            'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                            'style' => 'width: 170px;max-width: 200%;'
                        ),
                        //'translation_domain' => $this->getBundleName()
                    )),
                    array('show_suffix', 'checkbox', array(
                        'required' => false,
                        "label_attr" => array(
                            'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                            'style' => 'width: 170px;max-width: 200%;'
                        ),
                        //'translation_domain' => $this->getBundleName()
                    )),
                )
            ));
        }
        if (!$this->getHasDataModelDatasetMultipleDatas()) {
            $keys[] = array('dataset_detail_for_datas', 'sonata_type_immutable_array', array(
                // 'label' => false,
                'keys' => array(
                    array('detail_id', 'choice', array(
                        "multiple" => false, "expanded" => false, "required" => true,
                        'choices' => $details_for_datas_choices, // TODO preguntar al component type si el data es numeric or date
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'),
                        //'translation_domain' => $this->getBundleName()
                    )
                    ),
                    array('detail_label', 'choice', array(
                        'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                        "multiple" => false, "expanded" => false, "required" => true,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                        //'translation_domain' => $this->getBundleName()
                    )),
                    array('crossed_function', 'choice', array(
                        "multiple" => false, "expanded" => false, "required" => false,
                        'choices' => array(
                            'SUM' => 'SUM',
                            'AVG' => 'AVG',
                            'COUNT' => 'COUNT',
                            'MIN' => 'MIN',
                            'MAX' => 'MAX',
                        ),
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                        'translation_domain' => $this->getBundleName()
                    )),
                    array('text_align', 'choice', array(
                        "multiple" => false, "expanded" => false, "required" => true,
                        'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1',
                        ),
                        //'translation_domain' => $this->getBundleName()
                    )
                    ),
                    array('text_with', 'text', array(
                        "required" => false,
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-1'),
                        'attr' => array(
                            'placeholder' => 'px',
                            //'style' => 'width: 70px;'
                        ),
                        //'translation_domain' => $this->getBundleName()
                    )),
                    array('show_prefix', 'checkbox', array(
                        'required' => false,
                        "label_attr" => array(
                            'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                            'style' => 'width: 170px;max-width: 200%;'
                        ),
                        //'translation_domain' => $this->getBundleName()
                    )),
                    array('show_suffix', 'checkbox', array(
                        'required' => false,
                        "label_attr" => array(
                            'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                            'style' => 'width: 170px;max-width: 200%;'
                        ),
                        //'translation_domain' => $this->getBundleName()
                    )),
                )
            ));
        }
        if ($this->getHasDataModelDatasetMultipleDatas()) {
            $keys[] = array('dataset_details_for_datas', 'sonata_type_native_collection', array(
                'entry_type' => 'sonata_type_immutable_array',
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => array(
                    'keys' => array(
                        array('detail_id', 'choice', array(
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => $details_for_datas_choices, // TODO preguntar al component type si el data es numeric or date
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'),
                            //'translation_domain' => $this->getBundleName()
                        )),
                        array('detail_label', 'choice', array(
                            'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                            "multiple" => false, "expanded" => false, "required" => true,
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                            //'translation_domain' => $this->getBundleName()
                        )),
                        array('text_align', 'choice', array(
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',),
                            //'translation_domain' => $this->getBundleName()
                        )),
                        array('text_with', 'text', array(
                            "required" => false,
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                            'attr' => array(
                                'placeholder' => 'px',
                                //'style' => 'width: 70px;'
                            ),
                            //'translation_domain' => $this->getBundleName()
                        )),
                        array('show_prefix', 'checkbox', array(
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                            //'translation_domain' => $this->getBundleName()
                        )),
                        array('show_suffix', 'checkbox', array(
                            'required' => false,
                            "label_attr" => array(
                                'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                'style' => 'width: 170px;max-width: 200%;'
                            ),
                            //'translation_domain' => $this->getBundleName()
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
                        //'translation_domain' => $this->getBundleName()
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
                        //'translation_domain' => $this->getBundleName()
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
                        //'translation_domain' => $this->getBundleName()
                    )
                    ),
                    array('order_type', 'choice', array(
                        "multiple" => false, "expanded" => false, "required" => true,
                        'choices' => array('asc' => 'asc', 'desc' => 'desc'), // TODO add translator
                        "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-3'),
                        //'translation_domain' => $this->getBundleName()
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
                        //'translation_domain' => $this->getBundleName()
                    )),
                    array('items_per_page', 'number', array(
                        "label_attr" => array(
                            // 'class' => 'pull-left',
                            'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-3'
                        ),
                        //'translation_domain' => $this->getBundleName()
                    )),
                    array('max_paginator_links', 'number', array(
                        "label_attr" => array(
                            //'class' => 'pull-left',
                            'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-3'
                        ),
                        //'translation_domain' => $this->getBundleName()
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


    /**
     * @param $queryBuilder
     * @return DoctrineDbalPaginatorAdapter
     */
    protected function createPaginatorAdapterForQueryBuilder($queryBuilder)
    {
        return new DoctrineDbalPaginatorAdapter($queryBuilder);
    }

    /**
     * @param $queryBuilder
     * @return Pagerfanta
     */
    public function createPaginatorForQueryBuilder($queryBuilder)
    {
        $adapter = $this->createPaginatorAdapterForQueryBuilder($queryBuilder);

        $paginator = new Pagerfanta($adapter);

        return $paginator;
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
    public function getRenderableData(Request $request = null, Component $component, array $parameters = array(), $full_exportable_data = false)
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


            $paginator = $this->createPaginatorForQueryBuilder($queryBuilder);

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

        if ($this->getHasDataModelDatasetLabel()) {
            $datamodelDetails = array_merge($datamodelDetails, array($all['settings']['dataset_detail_for_labels']));
        }
        if ($this->getHasDataModelDatasetSeries()) {
            $datamodelDetails = array_merge($datamodelDetails, array($all['settings']['dataset_detail_for_series']));
        }
        if (!$this->getHasDataModelDatasetMultipleDatas()) {
            $datamodelDetails = array_merge($datamodelDetails, array($all['settings']['dataset_detail_for_datas']));
            $all['settings']['_data_crossed_function'] = $all['settings']['dataset_detail_for_datas']['crossed_function'];
        }
        if ($this->getHasDataModelDatasetMultipleDatas()) {
            $datamodelDetails = array_merge($datamodelDetails, $all['settings']['dataset_details_for_datas']);
        }

        $datamodelDetailsDescriptions = array();

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

        $all['settings']['_details_descriptions'] = $datamodelDetailsDescriptions;

        //--------------------------------

        $all['settings']['_labels'] = array(); // TODO
        $all['settings']['_series'] = array(); // TODO
        $all['settings']['_titles'] = array(); // TODO

        //--------------------------------

        if ($this->getHasDataModelDatasetLabel()) {

            if ($this->getHasDataModelDatasetMultipleDatas()) {

                $all['settings']['_datas_descriptions_type'] = 'label';
                $all['settings']['_datas_descriptions_by_label'] = $datamodelDetailsDescriptions;
                $all['settings']['_datas_descriptions_by_serie'] = array();
                $all['settings']['_datas_descriptions_by_data'] = array();

                $all['settings']['_labels'] = array(); // result[dataset_detail_for_labels]
                $all['settings']['_series'] = array(); // result[dataset_details_for_datas]
                $all['settings']['_titles'] = array(); // $datamodelDetailsDescriptions

                foreach ($all['settings']['dataset_details_for_datas'] as $col) {
                    $serie_detail_id = $col['detail_id'];
                    $serie_name = $datamodelDetailsDescriptions[$serie_detail_id]['title'];
                    $all['settings']['_series'][$serie_name] = array();
                }

                foreach ($all['data']['result'] as $row) {

                    $label_detail_id = $all['settings']['dataset_detail_for_labels']['detail_id'];

                    $all['settings']['_labels'][] = $datamodelDetailsDescriptions[$label_detail_id]['prefix'] .
                        $row[$datamodelDetailsDescriptions[$label_detail_id]['alias']] . $datamodelDetailsDescriptions[$label_detail_id]['suffix'];

                    foreach ($all['settings']['dataset_details_for_datas'] as $col) {
                        $serie_detail_id = $col['detail_id'];
                        $serie_name = $datamodelDetailsDescriptions[$serie_detail_id]['title'];
                        $all['settings']['_series'][$serie_name][] = $row[$datamodelDetailsDescriptions[$serie_detail_id]['alias']];
                    }

                }
            } /*else*/
            else { // !$this->getHasDataModelDatasetMultipleDatas()
                if ($this->getHasDataModelDatasetSeries()) {

                    $detail_for_labels = $all['settings']['dataset_detail_for_labels'];
                    $detail_for_series = $all['settings']['dataset_detail_for_series'];
                    $detail_for_datas = $all['settings']['dataset_detail_for_datas'];

                    $_crossed_labels = array();
                    $_crossed_series = array();

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
                        if (!isset($_crossed_series[$col_series_formatted])) {
                            $_crossed_series[$col_series_formatted] = array();
                        }

                        $col_datas = $row[$labelsDescriptions[$detail_for_datas['detail_id']]['alias']];
                        if (!isset($_crossed_series[$col_series_formatted][$col_label_formatted])) {
                            $_crossed_series[$col_series_formatted][$col_label_formatted] = array();
                        }
                        $_crossed_series[$col_series_formatted][$col_label_formatted][] = $col_datas;

                    }

                    //-------------------------------------------------------------------------------------------------

                    $all['settings']['_labels'] = array_values($_crossed_labels);
                    $all['settings']['_series'] = $_crossed_series;
                    $all['settings']['_titles'] = array(
                        'labels' => $labelsDescriptions[$all['settings']['dataset_detail_for_labels']['detail_id']]['title'],
                        'series' => $labelsDescriptions[$all['settings']['dataset_detail_for_series']['detail_id']]['title'],
                        'datas' => $labelsDescriptions[$all['settings']['dataset_detail_for_datas']['detail_id']]['title'],

                    );

                    $all['settings']['_datas_descriptions_type'] = 'data';
                    $all['settings']['_datas_descriptions_by_label'] = array();
                    $all['settings']['_datas_descriptions_by_serie'] = array();
                    $all['settings']['_datas_descriptions_by_data'] = array(
                        array(
                            'id' => $detail_for_datas['detail_id'],
                            'alias' => $labelsDescriptions[$detail_for_datas['detail_id']]['alias'],
                            'title' => $labelsDescriptions[$detail_for_datas['detail_id']]['title'],
                            'prefix' => $detail_for_datas['show_prefix'] ? $labelsDescriptions[$detail_for_datas['detail_id']]['prefix'] : '',
                            'suffix' => $detail_for_datas['show_prefix'] ? $labelsDescriptions[$detail_for_datas['detail_id']]['suffix'] : '',
                            'type' => $labelsDescriptions[$detail_for_datas['detail_id']]['type'],
                            'format' => $labelsDescriptions[$detail_for_datas['detail_id']]['format'],
                            'classification' => $labelsDescriptions[$detail_for_datas['detail_id']]['classification'],
                            'text_align' => isset($detail_for_datas['text_align']) ? $detail_for_datas['text_align'] : '',
                            'text_with' => isset($detail_for_datas['text_with']) ? $detail_for_datas['text_with'] : '',
                        )
                    );
                } else {
                    // TODO single detail

                    $detail_for_labels = $all['settings']['dataset_detail_for_labels'];
                    $detail_for_datas = $all['settings']['dataset_detail_for_datas'];

                    $_crossed_labels = array();
                    $_crossed_series = array();

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

                        $col_datas = $row[$labelsDescriptions[$detail_for_datas['detail_id']]['alias']];
                        if (!isset($_crossed_series[$col_label_formatted])) {
                            $_crossed_series[$col_label_formatted] = array();
                        }
                        $_crossed_series[$col_label_formatted][] = $col_datas;

                    }

                    //-------------------------------------------------------------------------------------------------

                    $all['settings']['_labels'] = array_values($_crossed_labels);
                    $all['settings']['_series'] = $_crossed_series;
                    $all['settings']['_titles'] = array(
                        'labels' => $labelsDescriptions[$all['settings']['dataset_detail_for_labels']['detail_id']]['title'],
                        'series' => [],
                        'datas' => $labelsDescriptions[$all['settings']['dataset_detail_for_datas']['detail_id']]['title'],
                    );

                    $all['settings']['_datas_descriptions_type'] = 'data';
                    $all['settings']['_datas_descriptions_by_label'] = array();
                    $all['settings']['_datas_descriptions_by_serie'] = array();
                    $all['settings']['_datas_descriptions_by_data'] = array(
                        array(
                            'id' => $detail_for_datas['detail_id'],
                            'alias' => $labelsDescriptions[$detail_for_datas['detail_id']]['alias'],
                            'title' => $labelsDescriptions[$detail_for_datas['detail_id']]['title'],
                            'prefix' => $detail_for_datas['show_prefix'] ? $labelsDescriptions[$detail_for_datas['detail_id']]['prefix'] : '',
                            'suffix' => $detail_for_datas['show_prefix'] ? $labelsDescriptions[$detail_for_datas['detail_id']]['suffix'] : '',
                            'type' => $labelsDescriptions[$detail_for_datas['detail_id']]['type'],
                            'format' => $labelsDescriptions[$detail_for_datas['detail_id']]['format'],
                            'classification' => $labelsDescriptions[$detail_for_datas['detail_id']]['classification'],
                            'text_align' => isset($detail_for_datas['text_align']) ? $detail_for_datas['text_align'] : '',
                            'text_with' => isset($detail_for_datas['text_with']) ? $detail_for_datas['text_with'] : '',
                        )
                    );
                }
            }

        }

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

        $exportableData = array(
            'title' => $component->getTitle(),
            'has_series' => null,
            'show_row_number' => null,
            'datas_descriptions_by' => null,
            'datas_descriptions' => null,
            'labels' => array(),
            'series' => array(),
        );

        //----------------------------------------------------------

        $formatter_helper = $all['formatter_helper'];

        $datamodel_details_descriptions = $all['settings']['_details_descriptions']; // TODO

        // TODO

        if ($this->getHasDataModelDatasetLabel()) {
            if ($this->getHasDataModelDatasetMultipleDatas()) {
                $exportableData['has_series'] = false;
                $exportableData['has_summaries'] = false;
                $exportableData['datas_descriptions_by'] = 'label';
                $labels_alias_ids = array();
                foreach ($datamodel_details_descriptions as $id => $lbd) {
                    $labels_alias_ids[$lbd['alias']] = $id;
                    $exportableData['labels'][] = $lbd['title'];
                    $exportableData['datas_descriptions'][] = $lbd;
                }

                foreach ($all['data']['result'] as $i => $row) {
                    $serie_id = $i;
                    $serie_data = array();
                    foreach ($labels_alias_ids as $alias => $id) {
                        $value = $row[$alias];
                        $format = $datamodel_details_descriptions[$id]['format'];
                        //$prefix = $exportableLabels[$alias]['prefix'];
                        //$suffix = $exportableLabels[$alias]['suffix'];
                        $txt = $formatter_helper->formatValue($value, $format);
                        $serie_data[] = $txt;
                    }
                    $exportableData['series'][$serie_id] = $serie_data;
                }
            } else {
                if ($this->getHasDataModelDatasetSeries()) {
                    $exportableData['has_series'] = true;
                    $exportableData['series_title'] = $all['settings']['_titles']['labels'] . ' / ' . $all['settings']['_titles']['series'] . ' => ' . $all['settings']['_titles']['datas'];
                    if (!empty($all['settings']['_summary_function'])) {
                        $exportableData['has_summaries'] = true;
                        $exportableData['datas_summaries'] = array(
                            'title' => $all['settings']['_summary_label'],
                            'title_text_align' => $all['settings']['_datas_descriptions_by_data'][0]['text_align'],
                            'title_prefix' => $all['settings']['_datas_descriptions_by_data'][0]['prefix'],
                            'title_suffix' => $all['settings']['_datas_descriptions_by_data'][0]['suffix'],
                            'for_label' => array(),
                            'for_serie' => array(),
                            'for_all' => null,
                        );
                    } else {
                        $exportableData['has_summaries'] = false;
                    }
                    $exportableData['datas_descriptions_by'] = 'data';

                    $exportableData['labels'] = $all['settings']['_labels'];
                    $exportableData['datas_descriptions'] = $all['settings']['_datas_descriptions_by_data'];

                    $summary_vertical_values = array();
                    $summary_final_values = array();

                    $format = $exportableData['datas_descriptions'][0]['format'];
                    //$prefix = $exportableLabels[$alias]['prefix'];
                    //$suffix = $exportableLabels[$alias]['suffix'];

                    foreach ($all['settings']['_series'] as $serie_id => $serie) {

                        $serie_data = array();

                        $summary_horizontal_values = array();

                        foreach ($all['settings']['_labels'] as $label) {

                            $values = isset($serie[$label]) ? $serie[$label] : array();

                            $value = $formatter_helper->summarizeValues($all['settings']['_summary_function'], $values);

                            $txt = $formatter_helper->formatValue($value, $format);
                            $serie_data[] = $txt;

                            $summary_horizontal_values = $formatter_helper->pushValuesToArray($summary_horizontal_values, $values);
                            if (!isset($summary_vertical_values[$label])) {
                                $summary_vertical_values[$label] = array();
                            }
                            $summary_vertical_values[$label] = $formatter_helper->pushValuesToArray($summary_vertical_values[$label], $values);
                            $summary_final_values = $formatter_helper->pushValuesToArray($summary_final_values, $values);
                        }
                        $exportableData['series'][$serie_id] = $serie_data;

                        $exportableData['datas_summaries']['for_serie'][$serie_id] = $formatter_helper->formatValue($formatter_helper
                            ->summarizeValues(
                                $all['settings']['_summary_function'],
                                $summary_horizontal_values
                            ), $format);
                    }

                    foreach ($all['settings']['_labels'] as $label) {
                        $exportableData['datas_summaries']['for_label'][$label] = $formatter_helper->formatValue($formatter_helper
                            ->summarizeValues(
                                $all['settings']['_summary_function'],
                                $summary_vertical_values[$label]
                            ), $format);
                    }
                    $exportableData['datas_summaries']['for_all'] = $formatter_helper->formatValue($formatter_helper
                        ->summarizeValues(
                            $all['settings']['_summary_function'],
                            $summary_final_values
                        ), $format);
                } else {
                    // TODO
                    $exportableData['has_series'] = true;
                    $exportableData['series_title'] = '';
                    if (!empty($all['settings']['_summary_function'])) {
                        $exportableData['has_summaries'] = true;
                        $exportableData['datas_summaries'] = array(
                            'title' => $all['settings']['_summary_label'],
                            'title_text_align' => $all['settings']['_datas_descriptions_by_data'][0]['text_align'],
                            'title_prefix' => $all['settings']['_datas_descriptions_by_data'][0]['prefix'],
                            'title_suffix' => $all['settings']['_datas_descriptions_by_data'][0]['suffix'],
                            'for_label' => array(),
                            'for_serie' => array(),
                            'for_all' => null,
                        );
                    } else {
                        $exportableData['has_summaries'] = false;
                    }
                    $exportableData['datas_descriptions_by'] = 'data';

                    $exportableData['labels'] = $all['settings']['_labels'];
                    $exportableData['datas_descriptions'] = $all['settings']['_datas_descriptions_by_data'];

                    $summary_vertical_values = array();
                    $summary_final_values = array();

                    $format = $exportableData['datas_descriptions'][0]['format'];
                    //$prefix = $exportableLabels[$alias]['prefix'];
                    //$suffix = $exportableLabels[$alias]['suffix'];

                    foreach ($all['settings']['_series'] as $serie_id => $serie) {

                        $serie_data = array();

                        $summary_horizontal_values = array();

                        foreach ($all['settings']['_labels'] as $label) {

                            $values = isset($serie[$label]) ? $serie[$label] : array();

                            $value = $formatter_helper->summarizeValues($all['settings']['_summary_function'], $values);

                            $txt = $formatter_helper->formatValue($value, $format);
                            $serie_data[] = $txt;

                            $summary_horizontal_values = $formatter_helper->pushValuesToArray($summary_horizontal_values, $values);
                            if (!isset($summary_vertical_values[$label])) {
                                $summary_vertical_values[$label] = array();
                            }
                            $summary_vertical_values[$label] = $formatter_helper->pushValuesToArray($summary_vertical_values[$label], $values);
                            $summary_final_values = $formatter_helper->pushValuesToArray($summary_final_values, $values);
                        }
                        $exportableData['series'][$serie_id] = $serie_data;

                        $exportableData['datas_summaries']['for_serie'][$serie_id] = $formatter_helper->formatValue($formatter_helper
                            ->summarizeValues(
                                $all['settings']['_summary_function'],
                                $summary_horizontal_values
                            ), $format);
                    }

                    foreach ($all['settings']['_labels'] as $label) {
                        $exportableData['datas_summaries']['for_label'][$label] = $formatter_helper->formatValue($formatter_helper
                            ->summarizeValues(
                                $all['settings']['_summary_function'],
                                $summary_vertical_values[$label]
                            ), $format);
                    }
                    $exportableData['datas_summaries']['for_all'] = $formatter_helper->formatValue($formatter_helper
                        ->summarizeValues(
                            $all['settings']['_summary_function'],
                            $summary_final_values
                        ), $format);
                }
            }
        }

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

        $show_row_number = $exportableData['show_row_number'];

        $has_summaries = $exportableData['has_summaries'];

        $dataDescriptionsType = $exportableData['datas_descriptions_by'];

        $dataDescriptions = $exportableData['datas_descriptions'];

        $labels = $exportableData['labels'];

        $series = $exportableData['series'];

        switch ($format) {
            case 'xls':
                $contentType = 'application/vnd.ms-excel';

                $content = '<html>' . "\r\n";
                $content .= '    <head>' . "\r\n";
                $content .= '        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\r\n";
                $content .= '        <meta name=ProgId content="Excel.Sheet">' . "\r\n";
                $content .= '        <meta name=Generator content="">' . "\r\n";
                $content .= '    </head>' . "\r\n";
                $content .= '    <body>' . "\r\n";
                $content .= '        <table border="1">' . "\r\n";
                $content .= '            <thead>' . "\r\n";
                $content .= '                <tr><td colspan="' . (count($labels) + ($has_summaries ? 1 : 0) + ($show_row_number ? 1 : 0) + ($has_series ? 1 : 0)) . '">' . htmlentities($title) . '</td></tr>' . "\r\n";
                $content .= '                <tr>';
                if ($show_row_number) {
                    $content .= '<td style="text-align: center;">#</td>';
                }
                if ($has_series) {
                    $series_title = $exportableData['series_title'];
                    $content .= '<td>' . htmlentities($series_title) . '</td>';
                }
                foreach ($labels as $label) {
                    $content .= '<td>' . htmlentities($label) . '</td>';
                }
                if ($has_summaries) {
                    $content .= '<td style="text-align: ' . $exportableData['datas_summaries']['title_text_align'] . '"> ( '
                        . htmlentities($exportableData['datas_summaries']['title']) . ' ) </td>';
                }
                $content .= '                </tr>' . "\r\n";
                $content .= '            </thead>' . "\r\n";
                $content .= '            <tbody>' . "\r\n";

                $row_number = 0;
                foreach ($series as $serie_id => $row) {
                    $row_number++;
                    $content .= '                <tr>';
                    $content .= $show_row_number ? '<td>' . $row_number . '</td>' : '';
                    $content .= $has_series ? '<td>' . htmlentities($serie_id) . '</td>' : '';
                    foreach ($row as $label => $value) {
                        if ($dataDescriptionsType == 'label') {
                            $idx = $label;
                        } elseif ($dataDescriptionsType == 'serie') {
                            $idx = $serie_id;
                        } else { // data
                            $idx = 0;
                        }
                        $txt = $dataDescriptions[$idx]['prefix'] . $value . $dataDescriptions[$idx]['suffix'];
                        $content .= '<td style="text-align: ' . $dataDescriptions[$idx]['text_align'] . ';">' . htmlentities($txt) . '</td>';
                    }
                    if ($has_summaries && count($exportableData['datas_summaries']['for_serie']) > 0) {
                        $content .= '<td style="text-align: ' . $exportableData['datas_summaries']['title_text_align'] . ';">'
                            . htmlentities($exportableData['datas_summaries']['title_prefix'])
                            . htmlentities($exportableData['datas_summaries']['for_serie'][$serie_id])
                            . htmlentities($exportableData['datas_summaries']['title_suffix'])
                            . '</td>';
                    }
                    $content .= '</tr>' . "\r\n";
                }
                if ($has_summaries && count($exportableData['datas_summaries']['for_label']) > 0) {
                    $content .= '                <tr>';
                    $content .= '<td style="text-align: right;" colspan="' . (($has_series ? 1 : 0) + ($show_row_number ? 1 : 0)) . '"> (' . htmlentities($exportableData['datas_summaries']['title']) . ')</td>';
                    foreach ($labels as $i => $label) {
                        $content .= '<td style="text-align: ' . $exportableData['datas_summaries']['title_text_align'] . ';">'
                            . htmlentities($exportableData['datas_summaries']['title_prefix'])
                            . htmlentities($exportableData['datas_summaries']['for_label'][$label])
                            . htmlentities($exportableData['datas_summaries']['title_suffix'])
                            . '</td>';
                    }
                    if (!is_null($exportableData['datas_summaries']['for_all'])) {
                        $content .= '<td style="text-align: ' . $exportableData['datas_summaries']['title_text_align'] . ';">'
                            . htmlentities($exportableData['datas_summaries']['title_prefix'])
                            . htmlentities(htmlentities($exportableData['datas_summaries']['for_all']))
                            . htmlentities($exportableData['datas_summaries']['title_suffix'])
                            . '</td>';
                    }

                    $content .= '</tr>' . "\r\n";
                }

                $content .= "            </tbody>" . "\r\n";
                $content .= "        </table>" . "\r\n";
                $content .= "    </body>" . "\r\n";
                $content .= "</html>";
                break;
            case
            'csv':
                $contentType = 'text/csv';

                $content = '"' . str_replace('"', '\"', $title) . '"' . "\r\n";

                if ($show_row_number) {
                    $content .= '' . ('#') . ';';
                }
                if ($has_series) {
                    $series_title = $exportableData['series_title'];
                    $content .= '' . (str_replace('"', '\"', $series_title)) . ';';
                }
                foreach ($labels as $i => $label) {
                    $content .= '' . (str_replace('"', '\"', $label)) . (($i < count($labels) - 1) ? ';' : '');
                }
                if ($has_summaries) {
                    $content .= ';' . (str_replace('"', '\"', $exportableData['datas_summaries']['title'])) . '';
                }
                $content .= "\r\n";

                $row_number = 0;
                foreach ($series as $serie_id => $row) {
                    $row_number++;
                    if ($show_row_number) {
                        $content .= '' . ($row_number) . ';';
                    }
                    if ($has_series) {
                        $content .= '' . (str_replace('"', '\"', $serie_id)) . ';';
                    }
                    $i = 0;
                    foreach ($row as $label => $value) {
                        if ($dataDescriptionsType == 'label') {
                            $idx = $label;
                        } elseif ($dataDescriptionsType == 'serie') {
                            $idx = $serie_id;
                        } else { // data
                            $idx = 0;
                        }
                        $txt = $dataDescriptions[$idx]['prefix'] . $value . $dataDescriptions[$idx]['suffix'];
                        $content .= '' . (str_replace('"', '\"', $txt)) . (($i < count($row) - 1) ? ';' : '');
                        $i++;
                    }
                    if ($has_summaries && count($exportableData['datas_summaries']['for_serie']) > 0) {
                        $content .= ';' . (str_replace('"', '\"',
                                $exportableData['datas_summaries']['title_prefix']
                                . $exportableData['datas_summaries']['for_serie'][$serie_id]
                                . $exportableData['datas_summaries']['title_suffix']
                            ));
                    }
                    $content .= "\r\n";
                }
                if ($has_summaries && count($exportableData['datas_summaries']['for_label']) > 0) {
                    if ($show_row_number) {
                        $content .= ';';
                    }
                    if ($has_series) {
                        $content .= '' . (str_replace('"', '\"', $exportableData['datas_summaries']['title'])) . ';';
                    }
                    foreach ($labels as $i => $label) {
                        $content .= '' . (str_replace('"', '\"',
                                $exportableData['datas_summaries']['title_prefix']
                                . $exportableData['datas_summaries']['for_label'][$label]
                                . $exportableData['datas_summaries']['title_suffix']
                            )) . (($i < count($row) - 1) ? ';' : '');
                    }
                    if (!is_null($exportableData['datas_summaries']['for_all'])) {
                        $content .= ';' . (str_replace('"', '\"',
                                $exportableData['datas_summaries']['title_prefix']
                                . $exportableData['datas_summaries']['for_all']
                                . $exportableData['datas_summaries']['title_suffix']
                            ));
                    }
                    $content .= "\r\n";
                }

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

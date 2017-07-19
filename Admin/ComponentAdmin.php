<?php

namespace TechPromux\Bundle\DynamicReportBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use TechPromux\Bundle\BaseBundle\Admin\Resource\BaseResourceAdmin;
use TechPromux\Bundle\DynamicReportBundle\Entity\Component;
use TechPromux\Bundle\DynamicReportBundle\Manager\ComponentManager;
use TechPromux\Bundle\DynamicReportBundle\Type\Component\BaseComponentType;

class ComponentAdmin extends BaseResourceAdmin
{

    protected $accessMapping = array(
        'copy' => 'COPY',
        'render' => 'RENDER',
        'execute' => 'EXECUTE',
        'export' => 'EXPORT',
        'saveas' => 'SAVEAS',
    );

    public function getParentAssociationMapping()
    {
        return 'report';
    }

    /**
     * @return ComponentManager
     */
    public function getResourceManager()
    {
        return parent::getResourceManager();
    }

    /**
     *
     * @return Component
     */
    public function getSubject()
    {
        return parent::getSubject();
    }

    //--------------------------------------------------------------------

    protected function configureRoutes(\Sonata\AdminBundle\Route\RouteCollection $collection)
    {
        if (!$this->isChild()) {
            $collection->clear();
        } else {
            $collection->clearExcept(array('list', 'create', 'edit', 'delete'));
            $collection->add('copy', $this->getRouterIdParameter() . '/copy');
            $collection->add('render', $this->getRouterIdParameter() . '/render');
            $collection->add('execute', $this->getRouterIdParameter() . '/execute');
            $collection->add('export', $this->getRouterIdParameter() . '/export');
            //$collection->add('saveas', $this->getRouterIdParameter() . '/saveas/{format}');
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {

        parent::configureDatagridFilters($datagridMapper);

        $datagridMapper
            ->add('title');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $type_choices = $this->getResourceManager()->getUtilDynamicReportManager()->getComponentsTypesChoicesWithoutGroups();

        $listMapper
            ->addIdentifier('name')
            ->add('title')
            ->add('component_type', 'choice', array(
                'choices' => $type_choices,
            ))
            ->add('enabled', null, array('editable' => true,
                'row_align' => 'center',
                'header_style' => 'width: 100px',
            ));


        parent::configureListFields($listMapper);

        $listMapper
            ->add('_action', 'actions', array(
                'label' => 'Actions',
                'row_align' => 'right',
                'header_style' => 'width: 120px',
                //'header_class' => 'fa fa-table',
                'actions' => array(
                    //'show' => array(),
                    'edit' => array(),
                    'copy' => array(
                        'template' => $this->getResourceManager()->getCoreBundleName() . ':Admin:CRUD/list__action_copy.html.twig'
                    ),
                    'delete' => array(),
                )
            ));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);

        $object = $this->getSubject();

        $datamodel_manager = $this->getResourceManager()->getDataModelManager();

        $parent = false;
        /* @var $parent \TechPromux\Bundle\DynamicReportBundle\Entity\Report */

        if ($object->getId() === null) {
            $parent = $this->getParent()->getSubject();
            $object->setReport($parent);
        } else {
            $parent = $object->getReport();
        }

        if ($object->getId()) {
            $formMapper->tab('General Settings');
        }
        $formMapper->with('General Settings 1', array("class" => "col-md-7 with-remove-box-header1"));
        $formMapper->add('report', 'text', array('disabled' => true,));
        $formMapper->add('name');
        $formMapper->add('title');
        $formMapper->add('enabled');
        $formMapper->end();
        $formMapper->with('General Settings 2', array("class" => "col-md-5 with-remove-box-header1"));
        $formMapper->add('templateContainer', 'choice', array(
            //'label' => $this->trans('Template Container'),
            'multiple' => false, 'required' => true, 'expanded' => false,
            'choices' => $this->getResourceManager()->getUtilDynamicReportManager()->getContainersNamesByTemplate($parent->getTemplateName()),
            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-8')
        ));

        $formMapper->add('position', null, array(
            //'label' => 'Template Container Order',
            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4')
        ));

        if (is_null($object->getId())) {

            $all_components_types = $this->getResourceManager()->getUtilDynamicReportManager()->getRegisteredComponentTypes();

            $map = array();
            foreach ($all_components_types as $ct) /* @var $ct BaseComponentType */ {
                if ($ct->getHasDataModelDataset())
                    $map[$ct->getId()] = array('datamodel');
                else {
                    $map[$ct->getId()] = array();
                }
            }
            $formMapper->add('component_type', 'sonata_type_choice_field_mask', array(
                //'label' => $this->trans('Component Type'),
                'choices' => $this->getResourceManager()->getUtilDynamicReportManager()->getComponentsTypesChoices(),
                'map' => $map,
                'required' => true,
            ));

            $formMapper->add('datamodel', 'entity', array(
                'class' => $datamodel_manager->getResourceClass(),
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use ($object, $datamodel_manager) {
                    $qb = $datamodel_manager->createQueryBuilder();
                    $qb->andWhere($qb->getRootAliases()[0] . '.enabled=1');
                    $qb->addOrderBy($qb->getRootAliases()[0] . '.title', 'ASC');
                    return $qb;
                }
            ));
            $formMapper->end();
        } else {
            $formMapper->add('component_type', 'text', array(
                'disabled' => true,
            ));
        }


        if (!is_null($object->getId())) {

            $component_type_name = $object->getComponentType();

            $component_type = $this->getResourceManager()->getUtilDynamicReportManager()->getComponentTypeById($component_type_name);

            /* @var $component_type BaseComponentType */

            if ($component_type->getHasDataModelDataset()) {

                $formMapper->add('datamodel', 'text', array(
                    'disabled' => true
                ));
            }

            $formMapper->end()->end();

            if ($component_type->getHasDataModelDataset()) {


                $formMapper->tab('tab.label_data_settings')
                    ->with('Data Settings', array("class" => "col-md-12 with-remove-box-header"));

                //--------------------------------------

                $datamodel_id = $object->getDatamodel()->getId();

                $details = $this->getResourceManager()->getDatamodelManager()->getEnabledDetailsDescriptionsFromDataModel($datamodel_id);

                $details_choices = array();

                $details_numeric_choices = array();

                $details_numeric_datetime_choices = array();

                foreach ($details as $dt) {
                    $details_choices[$dt['title'] . ' (' . $dt['abbreviation'] . ')'] = $dt['id'];
                    if ($dt['classification'] == 'number')
                        $details_numeric_choices[$dt['title'] . ' (' . $dt['abbreviation'] . ')'] = $dt['id'];
                    if ($dt['classification'] == 'number' || $dt['classification'] == 'datetime')
                        $details_numeric_datetime_choices[$dt['title'] . ' (' . $dt['abbreviation'] . ')'] = $dt['id'];
                }

                //-------------------------------------------------------------

                $keys = $this->getDefaultComponentSettingsKeysForEditForm();

                //-------------------------------------------------------------------------
                $formMapper
                    ->add('data_options', 'sonata_type_immutable_array', array(
                        'label' => false,
                        'keys' => $keys
                    ));

                $formMapper->end()->end();

                //-------------------------------------------------------

                $formMapper->tab('tab.label_' . $component_type->getId() . '_settings')
                    ->with('Component Settings', array("class" => "col-md-12 with-remove-box-header"));

                $keys_custom = $component_type->getCustomSettingsKeysForEditForm($object, array(
                    'details' => $details,
                    'details_numeric_choices' => $details_numeric_choices,
                    'details_numeric_datetime_choices' => $details_numeric_datetime_choices,
                ));

                $formMapper
                    ->add('component_options', 'sonata_type_immutable_array', array(
                        'label' => false,
                        'keys' => $keys_custom
                    ));

                $formMapper->end()->end();

                //--------------------------------------------
            } else {
                $formMapper->add('datamodel', 'entity', array(
                    'label' => $this->trans('DataModel to execute'),
                    'class' => $this->getResourceManager()->getDatamodelManager()->getResourceClass(),
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use ($object, $datamodel_manager) {
                        $qb = $datamodel_manager->createQueryBuilder();
                        $qb->andWhere($qb->getRootAlias() . '.enabled = true');
                        return $qb;
                    }
                ));
            }

        }
    }

    public function getTemplate($name)
    {

        switch ($name) {
            case 'list':
                return $this->getResourceManager()->getBundleName() . ':Admin:' . $this->getResourceManager()->getResourceName() . '/list.html.twig';
            case 'edit':
                $object = $this->getSubject();
                if ($object && $object->getId()) {
                    $component_type = $object->getComponentType();
                    $component = $this->getResourceManager()->getUtilDynamicReportManager()->getComponentTypeById($component_type);
                    /* @var $component BaseComponentType */
                    $alternative_template = $component->getTemplateForEditForm();
                    return $alternative_template;
                }
            default:
                break;
        }
        return parent::getTemplate($name);
    }

    /**
     * @param \Sonata\CoreBundle\Validator\ErrorElement $errorElement
     * @param Component $object
     */
    public function validate(\Sonata\CoreBundle\Validator\ErrorElement $errorElement, $object)
    {
        $object->setName($object->getTitle());

        parent::validate($errorElement, $object);

        $errorElement
            ->with('report')
            ->assertNotBlank()
            ->end()
            ->with('title')
            ->assertNotBlank()
            ->assertLength(array('min' => 3))
            ->end();

        $component_type_name = $object->getComponentType();

        $component_type = $this->getResourceManager()->getUtilDynamicReportManager()->getComponentTypeById($component_type_name);

        /* @var $component_type BaseComponentType */

        $component_type->validateComponent($errorElement, $object);
    }

    /**
     * @param Component $object
     */
    public function prePersist($object)
    {
        parent::prePersist($object); // TODO: Change the autogenerated stub

        $component_type_name = $object->getComponentType();

        $component_type = $this->getResourceManager()->getUtilDynamicReportManager()->getComponentTypeById($component_type_name);

        /* @var $component_type BaseComponentType */

        $object->setDataOptions($this->getDefaultComponentSettings());
        $object->setComponentOptions($component_type->getDefaultCustomSettings());

    }

    //-----------------------------------------------------------------------------------------------

    /**
     * @return array
     */
    protected function getDefaultComponentSettings()
    {
        $object = $this->getSubject();

        $component_type_name = $object->getComponentType();

        $component_type = $this->getResourceManager()->getUtilDynamicReportManager()->getComponentTypeById($component_type_name);
        /* @var $component_type BaseComponentType */

        $default_settings = array();

        if ($component_type->getHasDataModelDataset()) {

            $default_settings['dataset_type'] = $component_type->getDataModelDatasetType();

            $datamodel_id = $object->getDatamodel()->getId();

            $details = $this->getResourceManager()->getDatamodelManager()->getEnabledDetailsDescriptionsFromDataModel($datamodel_id);

            switch ($component_type->getDataModelDatasetType()) {
                case 'multiple':

                    $default_settings['dataset_multiple_detail_for_label'] = array(
                        'detail_id' => null,
                        'show_prefix' => true,
                        'show_suffix' => true,
                        'show_filter' => true,
                    );

                case 'multiple_without_label':

                    $default_settings['dataset_multiple_details_for_data'] = array();

                    foreach ($details as $dt) {
                        $default_settings['dataset_multiple_details_for_data'][] = array(
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

                    $default_settings['dataset_crossed_detail_for_label'] = array(
                        'detail_id' => null,
                        'show_prefix' => true,
                        'show_suffix' => true,
                        'show_filter' => true,
                    );
                    $default_settings['dataset_crossed_detail_for_data'] = array(
                        'detail_id' => null,
                        'show_prefix' => true,
                        'show_suffix' => true,
                        'show_filter' => true,
                    );
                    $default_settings['dataset_crossed_detail_for_series'] = array(
                        'detail_id' => null,
                        'show_prefix' => true,
                        'show_suffix' => true,
                        'show_filter' => true,
                    );

                    break;
                case 'single':

                    $default_settings['dataset_single_detail_for_label'] = array(
                        'detail_id' => null,
                        'show_prefix' => true,
                        'show_suffix' => true,
                        'show_filter' => true,
                    );
                    $default_settings['dataset_single_detail_for_data'] = array(
                        'detail_id' => null,
                        'show_prefix' => true,
                        'show_suffix' => true,
                        'show_filter' => true,
                    );
                    break;
            }

            $default_settings['filter_result_details'] = array();

            foreach ($details as $dt) {
                $default_settings['filter_result_details'][] = array(
                    'detail_id' => $dt['id'],
                );
            }
            $default_settings['order_result_details'] = array();


        }

        $default_settings['export_data_options'] = $component_type->getExportablesFormats();

        return $default_settings;

    }

    /**
     * @return array
     */
    protected function getDataModelDetailsChoices()
    {
        $object = $this->getSubject();

        return $this->getResourceManager()->getDataModelDetailsChoices($object);
    }

    /**
     * @return array
     */
    public function getDefaultComponentSettingsKeysForEditForm()
    {
        $object = $this->getSubject();

        $component_type_name = $object->getComponentType();

        $component_type = $this->getResourceManager()->getUtilDynamicReportManager()->getComponentTypeById($component_type_name);
        /* @var $component_type BaseComponentType */

        //-----------------------------------------------------------

        $keys = array();

        if ($component_type->getHasDataModelDataset()) {

            $details = $this->getDataModelDetailsChoices();

            $details_for_labels_choices = $details['details_for_labels_choices'];
            $details_for_data_choices = $details['details_for_data_choices'];
            $details_for_series_choices = $details['details_for_series_choices'];

            $default_settings['dataset_type'] = $component_type->getDataModelDatasetType();

            switch ($component_type->getDataModelDatasetType()) {
                case 'multiple':

                    $keys[] = array('dataset_multiple_detail_for_label', 'sonata_type_immutable_array', array(
                        //'label' => false,
                        'keys' => array(
                            array('detail_id', 'choice', array(
                                //'label' => 'Detail',
                                "multiple" => false, "expanded" => false, "required" => true,
                                'choices' => $details_for_labels_choices, // TODO preguntar al component type si el data es numeric or date
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'),
                            )
                            ),
                            array('detail_label', 'choice', array(
                                //'label' => 'Label Header',
                                'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                                "multiple" => false, "expanded" => false, "required" => true,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                            )),
                            array('text_align', 'choice', array(
                                //'label' => 'Text Align',
                                "multiple" => false, "expanded" => false, "required" => true,
                                'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                ),
                            )
                            ),
                            array('text_with', 'text', array(
                                //'label' => 'Width',
                                "required" => false,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                                'attr' => array(
                                    'placeholder' => 'px',
                                    //'style' => 'width: 70px;'
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
                                //'label' => 'Suffix',
                                'required' => false,
                                "label_attr" => array(
                                    'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                    'style' => 'width: 170px;max-width: 200%;'
                                ),
                            )),
                        )
                    ));

                case 'multiple_without_label':

                    $keys[] = array('dataset_multiple_details_for_data', 'sonata_type_native_collection', array(
                        'entry_type' => 'sonata_type_immutable_array',
                        'allow_add' => true,
                        'allow_delete' => true,
                        'entry_options' => array(
                            'keys' => array(
                                array('detail_id', 'choice', array(
                                    //'label' => 'Detail',
                                    "multiple" => false, "expanded" => false, "required" => true,
                                    'choices' => $details_for_data_choices, // TODO preguntar al component type si el data es numeric or date
                                    "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'),
                                )
                                ),
                                array('detail_label', 'choice', array(
                                    //'label' => 'Label Header',
                                    'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                                    "multiple" => false, "expanded" => false, "required" => true,
                                    "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                                )),
                                array('text_align', 'choice', array(
                                    //'label' => 'Text Align',
                                    "multiple" => false, "expanded" => false, "required" => true,
                                    'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                                    "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                    ),
                                )
                                ),
                                array('text_with', 'text', array(
                                    //'label' => 'Width',
                                    "required" => false,
                                    "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                                    'attr' => array(
                                        'placeholder' => 'px',
                                        //'style' => 'width: 70px;'
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
                                    //'label' => 'Suffix',
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

                    $keys[] = array('dataset_crossed_detail_for_label', 'sonata_type_immutable_array', array(
                        //'label' => false,
                        'keys' => array(
                            array('detail_id', 'choice', array(
                                //'label' => 'Detail',
                                "multiple" => false, "expanded" => false, "required" => true,
                                'choices' => $details_for_labels_choices, // TODO preguntar al component type si el data es numeric or date
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'),
                            )
                            ),
                            array('detail_label', 'choice', array(
                                //'label' => 'Label Header',
                                'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                                "multiple" => false, "expanded" => false, "required" => true,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                            )),
                            array('text_align', 'choice', array(
                                //'label' => 'Text Align',
                                "multiple" => false, "expanded" => false, "required" => true,
                                'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                ),
                            )
                            ),
                            array('text_with', 'text', array(
                                //'label' => 'Width',
                                "required" => false,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                                'attr' => array(
                                    'placeholder' => 'px',
                                    //'style' => 'width: 70px;'
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
                                //'label' => 'Suffix',
                                'required' => false,
                                "label_attr" => array(
                                    'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                    'style' => 'width: 170px;max-width: 200%;'
                                ),
                            )),
                        )
                    ));

                    $keys[] = array('dataset_crossed_detail_for_data', 'sonata_type_immutable_array', array(
                        // 'label' => false,
                        'keys' => array(
                            array('detail_id', 'choice', array(
                                //'label' => 'Detail',
                                "multiple" => false, "expanded" => false, "required" => true,
                                'choices' => $details_for_data_choices, // TODO preguntar al component type si el data es numeric or date
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'),
                            )
                            ),
                            array('detail_label', 'choice', array(
                                //'label' => 'Label Header',
                                'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                                "multiple" => false, "expanded" => false, "required" => true,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                            )),
                            array('text_align', 'choice', array(
                                //'label' => 'Text Align',
                                "multiple" => false, "expanded" => false, "required" => true,
                                'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                ),
                            )
                            ),
                            array('text_with', 'text', array(
                                //'label' => 'Width',
                                "required" => false,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                                'attr' => array(
                                    'placeholder' => 'px',
                                    //'style' => 'width: 70px;'
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
                                //'label' => 'Suffix',
                                'required' => false,
                                "label_attr" => array(
                                    'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                    'style' => 'width: 170px;max-width: 200%;'
                                ),
                            )),
                        )
                    ));

                    $keys[] = array('dataset_crossed_detail_for_series', 'sonata_type_immutable_array', array(
                        //'label' => false,
                        'keys' => array(
                            array('detail_id', 'choice', array(
                                //'label' => 'Detail',
                                "multiple" => false, "expanded" => false, "required" => true,
                                'choices' => $details_for_series_choices, // TODO preguntar al component type si el data es numeric or date
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'),
                            )
                            ),
                            array('detail_label', 'choice', array(
                                //'label' => 'Label Header',
                                'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                                "multiple" => false, "expanded" => false, "required" => true,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                            )),
                            array('text_align', 'choice', array(
                                //'label' => 'Text Align',
                                "multiple" => false, "expanded" => false, "required" => true,
                                'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                ),
                            )
                            ),
                            array('text_with', 'text', array(
                                //'label' => 'Width',
                                "required" => false,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                                'attr' => array(
                                    'placeholder' => 'px',
                                    //'style' => 'width: 70px;'
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
                                //'label' => 'Suffix',
                                'required' => false,
                                "label_attr" => array(
                                    'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                    'style' => 'width: 170px;max-width: 200%;'
                                ),
                            )),
                        )
                    ));

                    break;
                case 'single':

                    $keys[] = array('dataset_single_detail_for_label', 'sonata_type_immutable_array', array(
                        //'label' => false,
                        'keys' => array(
                            array('detail_id', 'choice', array(
                                //'label' => 'Detail',
                                "multiple" => false, "expanded" => false, "required" => true,
                                'choices' => $details_for_labels_choices, // TODO preguntar al component type si el data es numeric or date
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'),
                            )
                            ),
                            array('detail_label', 'choice', array(
                                //'label' => 'Label Header',
                                'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                                "multiple" => false, "expanded" => false, "required" => true,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                            )),
                            array('text_align', 'choice', array(
                                //'label' => 'Text Align',
                                "multiple" => false, "expanded" => false, "required" => true,
                                'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                ),
                            )
                            ),
                            array('text_with', 'text', array(
                                //'label' => 'Width',
                                "required" => false,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                                'attr' => array(
                                    'placeholder' => 'px',
                                    //'style' => 'width: 70px;'
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
                                //'label' => 'Suffix',
                                'required' => false,
                                "label_attr" => array(
                                    'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                    'style' => 'width: 170px;max-width: 200%;'
                                ),
                            )),
                        )
                    ));

                    $keys[] = array('dataset_single_detail_for_data', 'sonata_type_immutable_array', array(
                        // 'label' => false,
                        'keys' => array(
                            array('detail_id', 'choice', array(
                                //'label' => 'Detail',
                                "multiple" => false, "expanded" => false, "required" => true,
                                'choices' => $details_for_data_choices, // TODO preguntar al component type si el data es numeric or date
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-4'),
                            )
                            ),
                            array('detail_label', 'choice', array(
                                //'label' => 'Label Header',
                        'choices' => array("title" => "title", "abbreviation" => "abbreviation"), // TODO translator y manager
                                "multiple" => false, "expanded" => false, "required" => true,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                            )),
                            array('text_align', 'choice', array(
                                //'label' => 'Text Align',
                                "multiple" => false, "expanded" => false, "required" => true,
                                'choices' => array('left' => 'left', 'center' => 'center', 'right' => 'right'), // TODO add translator
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                ),
                            )
                            ),
                            array('text_with', 'text', array(
                                //'label' => 'Width',
                                "required" => false,
                                "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2'),
                                'attr' => array(
                                    'placeholder' => 'px',
                                    //'style' => 'width: 70px;'
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
                                //'label' => 'Suffix',
                                'required' => false,
                                "label_attr" => array(
                                    'data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-2',
                                    'style' => 'width: 170px;max-width: 200%;'
                                ),
                            )),
                        )
                    ));


                    break;
            }

            $keys[] = array('filter_result_details', 'sonata_type_native_collection', array(
                //'label' => $this->trans('Details to Order Results'),
                'entry_type' => 'sonata_type_immutable_array',
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => array(
                    'keys' => array(
                        array('detail_id', 'choice', array(
                            'label' => 'Detail',
                            "multiple" => false, "expanded" => false, "required" => true,
                            'choices' => $details_for_labels_choices,
                            "label_attr" => array('data-ctype-modify' => 'parent', 'data-ctype-modify-parent-addclass' => 'col-md-7'),
                        )
                        )
                    ),
                )
            ));

            $keys[] = array('order_result_details', 'sonata_type_native_collection', array(
                //'label' => $this->trans('Details to Order Results'),
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

        }

        $keys[] = array('export_data_options', 'choice', array(
            //'label' => $this->trans('Formats to Allow Export Data'),
            'choices' => $component_type->getExportablesFormats(),
            'multiple' => true, 'expanded' => true, 'required' => false
        ));

        return $keys;

    }

}

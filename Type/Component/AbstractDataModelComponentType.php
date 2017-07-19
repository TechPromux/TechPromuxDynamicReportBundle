<?php

namespace TechPromux\Bundle\DynamicReportBundle\Type\Component;

use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TechPromux\Bundle\DynamicQueryBundle\Entity\DataModelDetail;
use TechPromux\Bundle\DynamicQueryBundle\Type\ConditionalOperator\BaseConditionalOperatorType;
use TechPromux\Bundle\DynamicReportBundle\Entity\Component;
use TechPromux\Bundle\DynamicReportBundle\Manager\ComponentManager;
use TechPromux\Bundle\DynamicReportBundle\Manager\UtilDynamicReportManager;

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
            'xml' => 'xml',
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
            'xml' => 'fa-file-code-o',
            'csv' => 'fa-file-text-o',
            'json' => 'fa-file-o',
        );
    }
    //-----------------------------------------------------------------------------

    /**
     * @param Request|null $request
     * @param Component $component
     * @param array $parameters
     *
     * @return array
     */
    public function createComponentData(Request $request = null, Component $component, array $parameters = array())
    {

        // TODO ver si va aqui o en render solamente
        //TODO $parameters['extras']['details_for_filter'] = $this->detailsDescriptionsToFilterBy($component);
        // crear los filtros_form para el render.....

        $filters_form = $this->createFiltersForm($request, $component, $parameters);

        $filters_form->handleRequest($request); // or handleRequest?

        if (count($filters_form->getErrors()) > 0 && !$filters_form->isValid()) { // if ($request->isXmlHttpRequest())
            throw new \Exception("ERROR!!!. Filter data isn´t valid");
        }

        $filter_form_data = $filters_form->getData();

        //--------------------------

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

        //--------------------------

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

        //----------------------------------
        // TODO preguntar si lo quiere paginado o no (al component)

        $result = array();

        if ($this->getDataModelDatasetResultPaginated()) {
            $paginator = $this->getUtilDynamicReportManager()->getComponentManager()->getDatamodelManager()->createPaginatorForQueryBuilder($queryBuilder);

            $page = $request->get('_page', 1); // ver como obtener la pagina por defecto de la configuracion (obtenerlo del filter data)
            $items_per_page = $request->get('_items_per_page', 32); // obtener la cantidad por defecto de la configuracion

            $paginator->setMaxPerPage($items_per_page);
            $rowCount = $queryBuilder->execute()->rowCount();

            if (intval($page) * $paginator->getMaxPerPage() <= $rowCount || ((intval($page) - 1) * $paginator->getMaxPerPage() < $rowCount)) {
                $paginator->setCurrentPage($page);
            } else {
                $paginator->setCurrentPage(1);
            }

            $currentPageResults = $paginator->getCurrentPageResults();

            $result = array(
                'paginator' => $paginator,
                'result' => $currentPageResults
            );
        } else {
            $data = $queryBuilder->execute()->fetchAll();

            return array(
                'result' => $data
            );
        }

        return $result;

    }


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

        $filter_result_details = $dataOptions['filter_result_details'];

        $datamodel_details_descriptions = $this->getUtilDynamicReportManager()
            ->getComponentManager()
            ->getDataModelManager()
            ->getEnabledDetailsDescriptionsFromDataModel($component->getDatamodel());

        $i = 0;
        foreach ($filter_result_details as $filter) {

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

            if (in_array($detail['type'], array('date', 'time', 'datetime'))) {
                $filters_form_builder->add("value_" . $i, $detail['type'], array(
                    'label' => false,
                    'required' => false,
                    'widget' => 'single_text',
                    'attr' => array('class' => $detail['type'], 'type' => $detail['type'])
                ));
            }
            if (in_array($detail['type'], array('boolean'))) {
                $filters_form_builder->add("value_" . $i, 'choice', array(
                    'label' => false,
                    'required' => false,
                    'choices' => array(
                        '0' => 'false',
                        '1' => 'true',
                    ),
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

        $filter_result_details = $dataOptions['filter_result_details'];

        $datamodel_details_descriptions = $this->getUtilDynamicReportManager()
            ->getComponentManager()
            ->getDataModelManager()
            ->getEnabledDetailsDescriptionsFromDataModel($component->getDatamodel());

        $operators = $this->getUtilDynamicReportManager()->getComponentManager()
            ->getDatamodelManager()->getUtilDynamicQueryManager()->getRegisteredConditionalOperators();

        $filters_by = array();

        $i = 0;
        foreach ($filter_result_details as $filter) {
            if (isset($filter_form_data['id_' . $i])) {
                $detail_id = $filter_form_data['id_' . $i];

                $operator_id = $filter_form_data['operator_' . $i];

                $value = $filter_form_data['value_' . $i];

                $detail_description = $datamodel_details_descriptions[$detail_id];

                if (isset($operators[$operator_id])) {
                    $operator = $operators[$operator_id];
                    /* @var $operator BaseConditionalOperatorType */

                    if ($operator->getIsUnary() || (!$operator->getIsUnary() && !empty($value))) {
                        if ($detail_description['type'] == 'date') {
                            $value = $value->format('Y-m-d') ? $value->format('Y-m-d') : $value->format('Y-m-d 00:00:00');
                        } else if ($detail_description['type'] == 'time') {
                            $value = $value->format('H:i:s') ? $value->format('H:i:s') : $value->format('Y-m-d H:i:s');
                        } else if ($detail_description['type'] == 'datetime') {
                            $value = $value->format('Y-m-d H:i:s');
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

        $order_result_details = $dataOptions['order_result_details'];

        foreach ($order_result_details as $od) {
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
     * @param Request|null $request
     * @param Component $component
     * @param array $parameters
     * @return array
     */
    protected function getMergedDataParametersAndSettings(Request $request = null, Component $component, array $parameters = array())
    {
        $all = parent::getMergedDataParametersAndSettings($request, $component, $parameters); // TODO: Change the autogenerated stub

        $all['extras']['datamodel_details_descriptions'] = $this->getUtilDynamicReportManager()->getComponentManager()->getDataModelManager()->getEnabledDetailsDescriptionsFromDataModel($component->getDatamodel());
        $all['extras']['formatter_helper'] = $this->getUtilDynamicReportManager()->getComponentManager()->getDatamodelManager()->getUtilDynamicQueryManager();
        $all['extras']['locale'] = $this->getUtilDynamicReportManager()->getLocaleFromAuthenticatedUser();

        return $all;
    }



    //-----------------------------------------------------------------


    /**
     *
     * @param \TechPromux\Bundle\DynamicReportBundle\Entity\Component $component
     * @param \Doctrine\DBAL\Query\QueryBuilder $queryBuilder
     * @param array $format
     * @return array
     */
    public function createExportableData2(\TechPromux\Bundle\DynamicReportBundle\Entity\Component $component, \Doctrine\DBAL\Query\QueryBuilder $queryBuilder)
    {

        // OBTAIN SETTINGS

        $settings = $component->getSettings();

        $result = $queryBuilder->execute()->fetchAll();

        $datamodel_details_descriptions = $this->getComponentManager()->getReportManager()->getDataModelManager()->descriptionForPublicDetailsFromQuery($component->getQuery()->getId());

        $data = array(
            'title' => $component->getTitle(),
            'labels' => array(),
            'data' => array()
        );

        foreach ($settings['details_for_labels'] as $d) {
            if (isset($datamodel_details_descriptions[$d['detail_id']])) {
                $data['labels'][] = array(
                    'label' => $datamodel_details_descriptions[$d['detail_id']][$d['detail_label']],
                    'type' => $datamodel_details_descriptions[$d['detail_id']]['type'],
                    //'format' => $datamodel_details_descriptions[$d['detail_id']]['format'],
                    'prefix' => ($d['show_prefix'] == true) ? $datamodel_details_descriptions[$d['detail_id']]['prefix'] : '',
                    'suffix' => ($d['show_suffix'] == true) ? $datamodel_details_descriptions[$d['detail_id']]['suffix'] : '',
                );
            }
        }

        foreach ($result as $row) {
            $row_data = array();
            foreach ($settings['details_for_labels'] as $d) {
                if (isset($datamodel_details_descriptions[$d['detail_id']])) {
                    //$col_format = $datamodel_details_descriptions[$d['detail_id']]['format'];
                    //$col_prefix = ($d['show_prefix'] == true) ? $datamodel_details_descriptions[$d['detail_id']]['prefix'] : '';
                    //$col_suffix = ($d['show_suffix'] == true) ? $datamodel_details_descriptions[$d['detail_id']]['suffix'] : '';
                    $col = $row[$datamodel_details_descriptions[$d['detail_id']]['sql_alias']];
                    $row_data[] = $col; //$this->getComponentManager()->getReportManager()->formatValue($col, $col_format, $col_prefix, $col_suffix);
                }
            }
            $data['data'][] = $row_data;
        }

        return $data;
    }

    public function detailsDescriptionsToFilterBy2(\TechPromux\Bundle\DynamicReportBundle\Entity\Component $component)
    {

        $settings = $component->getSettings();

        $details_for_labels = $settings['details_for_labels'];

        $details_for_filter = array();

        foreach ($details_for_labels as $detail) {
            if ($detail['show_filter'] == true) {
                $query_detail = $this->getComponentManager()->getReportManager()->getDataModelManager()->findQueryDetailById($detail['detail_id']);
                if ($query_detail && $query_detail->getIsPublic()) {
                    $details_for_filter[$detail['detail_id']] = array(
                        'id' => $detail['detail_id'],
                        'type' => $this->getComponentManager()->getReportManager()->getDataModelManager()->typeForQueryDetail($query_detail),
                        'title' => $query_detail->getTitle()
                    );
                }
            }
        }

        return $details_for_filter;
    }

    /**
     * }
     *
     * @param Component $component
     * @param array $data
     * @param array $format
     * @return Response
     */
    public function renderExportableResponse2(Component $component, $data, $format)
    {

        $has_series = $this->getHasSeriesData($component);
        $filename = strtolower(str_replace(' ', '_', $component->getTitle())) . '_' . date('Y_m_d_H_i_s', strtotime('now')) . '.' . $format;
        $content = "";
        $contentType = "";
        switch ($format) {
            case 'xls':
                $contentType = 'application/vnd.ms-excel';
                $content = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name=ProgId content="Excel.Sheet"><meta name=Generator content=""></head><body>';
                $content .= '<table border="1">';
                $content .= '<tr><td colspan="' . (count($data['labels']) + ($has_series ? 1 : 0)) . '">' . htmlentities($data['title']) . '</td></tr>';
                $content .= '<tr>' . ($has_series ? '<td></td>' : '');
                foreach ($data['labels'] as $i => $l) {
                    $content .= '<td>' . htmlentities($l['label']) . '</td>';
                }
                $content .= '</tr>';
                foreach ($data['data'] as $i => $r) {
                    $content .= '<tr>' . ($has_series ? '<td>' . htmlentities($i) . '</td>' : '');
                    foreach ($r as $j => $v) {
                        $content .= '<td>' . htmlentities($v) . '</td>';
                    }
                    $content .= '</tr>';
                }
                $content .= "</table>";
                $content .= "</body></html>";
                break;
            case 'xml':
                $contentType = 'text/xml';
                $content = '<?xml version="1.0" ?>';
                $content .= '<dataset>';
                $content .= '<title>' . htmlentities($data['title']) . '</title>';
                $content .= '<labels>';
                foreach ($data['labels'] as $i => $l) {
                    $content .= '<item>' . $l['label'] . '</item>';
                }
                $content .= '</labels>';
                $content .= '<data>';
                foreach ($data['data'] as $i => $r) {
                    $content .= '<serie name="' . $i . '">';
                    foreach ($r as $j => $v) {
                        $content .= '<item type="' . htmlentities($data['labels'][$j]['type']) . '" prefix="' . htmlentities($data['labels'][$j]['prefix']) . '" suffix="' . htmlentities($data['labels'][$j]['suffix']) . '">' . htmlentities($v) . '</item>';
                    }
                    $content .= '</serie>';
                }
                $content .= '</data>';
                $content .= '</dataset>';
                break;
            case 'csv':
                $contentType = 'text/csv';
                $content = '"' . str_replace('"', '\"', $data['title']) . '"' . '|'; // TODO cambiar | por \n
                foreach ($data['labels'] as $i => $l) {
                    $content .= '"' . str_replace('"', '\"', $l['label']) . '"' . (($i < count($data['labels']) - 1) ? ';' : '|');
                }
                foreach ($data['data'] as $i => $r) {
                    $content .= '"' . str_replace('"', '\"', $i) . '";';
                    foreach ($r as $j => $v) {
                        $content .= '"' . str_replace('"', '\"', $v) . '"' . (($j < count($r) - 1) ? ';' : '');
                    }
                    $content .= (($i < count($data['data']) - 1) ? '|' : '');
                }
                break;
            case 'json':
                $contentType = 'application/json';
                $content = '{';
                $content .= '"title":' . json_encode($data['title']) . ',';
                $content .= '"labels": [';
                foreach ($data['labels'] as $i => $l) {
                    $content .= json_encode($l['label']) . (($i < count($data['labels']) - 1) ? ',' : '],');
                }
                $content .= '"data": [';
                foreach ($data['data'] as $i => $r) {
                    $content .= '{ serie: ' . json_encode($i) . ' , items: [';
                    foreach ($r as $j => $v) {
                        $content .= json_encode($v) . (($j < count($r) - 1) ? ',' : '');
                    }
                    $content .= (($i < count($data['data']) - 1) ? ']},' : ']}]');
                }
                $content .= '}';
                break;
            default:
                throw new \RuntimeException('Invalid format');
        }
        return new Response($content, 200, array(
            'Content-Type' => $contentType,
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
        ));
    }

}

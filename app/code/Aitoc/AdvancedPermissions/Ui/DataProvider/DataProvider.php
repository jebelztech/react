<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Ui\DataProvider;

use Aitoc\AdvancedPermissions\Helper\Data as AdvancedPermissionHelper;
use Aitoc\AdvancedPermissions\Ui\DataProvider\AddFilterHelper\BaseAddFilterHelper;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as CoreDataProvider;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;

/**
 * Class DataProvider
 */
class DataProvider extends CoreDataProvider
{
    /**
     * @var AdvancedPermissionHelper
     */
    protected $helper;

    /**
     * @var BaseAddFilterHelper
     */
    protected $baseAddFilterHelper;

    /**
     * DataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Reporting $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param AdvancedPermissionHelper $helper
     * @param BaseAddFilterHelper $baseAddFilterHelper
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        AdvancedPermissionHelper $helper,
        BaseAddFilterHelper $baseAddFilterHelper,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );

        $this->helper = $helper;
        $this->baseAddFilterHelper = $baseAddFilterHelper;
    }

    /**
     * Returns search criteria
     *
     * @return SearchCriteria
     */
    public function getSearchCriteria()
    {
        if (!$this->searchCriteria) {
            $searchCriteria = parent::getSearchCriteria();

            if ($this->helper->isAdvancedPermissionEnabled()) {
                $newSearchCriteria = $this->addAdvancedPermissionFiltersIfRequired($searchCriteria);

                if ($newSearchCriteria) {
                    $searchCriteria = $newSearchCriteria;
                }
            }

            $this->searchCriteria = $searchCriteria;
        }

        return $this->searchCriteria;
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return SearchCriteria|null
     */
    protected function addAdvancedPermissionFiltersIfRequired(SearchCriteria $searchCriteria)
    {
        return $this->addStoreFilterIfRequired($searchCriteria);
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return SearchCriteria|null
     */
    private function addStoreFilterIfRequired(SearchCriteria $searchCriteria)
    {
        return $this->addFilterIfRequired($searchCriteria, $this->baseAddFilterHelper);
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @param BaseAddFilterHelper $filterHelper
     * @return SearchCriteria|null
     */
    protected function addFilterIfRequired(SearchCriteria $searchCriteria, BaseAddFilterHelper $filterHelper)
    {
        $filter = $filterHelper->getFilterIfRequired(
            $searchCriteria,
            $this->filterBuilder,
            $this->name
        );

        if (!$filter) {
            return null;
        }

        $this->addFilter($filter);
        $searchCriteriaBuilder = $this->searchCriteriaBuilder;

        $this->reinitCriteriaBuilderBySearchCriteria($searchCriteriaBuilder, $searchCriteria);
        $searchCriteria = $searchCriteriaBuilder->create();
        $searchCriteria->setRequestName($this->name);

        return $searchCriteria;
    }

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SearchCriteria $searchCriteria
     */
    private function reinitCriteriaBuilderBySearchCriteria(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SearchCriteria $searchCriteria
    ) {
        $pageSize = $searchCriteria->getPageSize();
        $currentPage = $searchCriteria->getCurrentPage();

        $searchCriteriaBuilder
            ->setCurrentPage($currentPage)
            ->setPageSize($pageSize);

        $sortOrders = $searchCriteria->getSortOrders();
        $this->addSortOrders($searchCriteriaBuilder, $sortOrders);

        $filterGroups = $searchCriteria->getFilterGroups();
        $this->addFiltersBySearchCriteriaFilterGroupsIfAllowed($filterGroups);
    }

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrder[]|null $searchCriteriaSortOrders
     */
    private function addSortOrders(SearchCriteriaBuilder $searchCriteriaBuilder, $searchCriteriaSortOrders)
    {
        foreach ($searchCriteriaSortOrders as $searchCriteriaSortOrder) {
            $this->addsearchCriteriaBuilderSortOrderBySearchCriteriaSortOrder(
                $searchCriteriaBuilder,
                $searchCriteriaSortOrder
            );
        }
    }

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrder $searchCriteriaSortOrder
     */
    private function addsearchCriteriaBuilderSortOrderBySearchCriteriaSortOrder(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrder $searchCriteriaSortOrder
    ) {
        $field = $searchCriteriaSortOrder->getField();
        $direction = $searchCriteriaSortOrder->getDirection();

        $searchCriteriaBuilder->addSortOrder($field, $direction);
    }

    /**
     * @param FilterGroup[] $filterGroups
     */
    private function addFiltersBySearchCriteriaFilterGroupsIfAllowed($filterGroups)
    {
        $restrictionField = $this->baseAddFilterHelper->getBindedFieldId();
        $allowedFieldValues = $this->baseAddFilterHelper->getAllowedFieldIds();

        foreach ($filterGroups as $filterGroup) {
            $this->addFiltersByFilterGroupIfAllowed($filterGroup, $restrictionField, $allowedFieldValues);
        }
    }

    /**
     * @param FilterGroup $filterGroup
     * @param string $restrictionFieldName
     * @param array $allowedFieldValues
     */
    private function addFiltersByFilterGroupIfAllowed(
        FilterGroup $filterGroup,
        $restrictionFieldName,
        $allowedFieldValues
    ) {
        $filters = $filterGroup->getFilters();

        if (!$filters) {
            return;
        }

        foreach ($filters as $filter) {
            if (!$this->isAllowedFilter($filter, $restrictionFieldName, $allowedFieldValues)) {
                continue;
            }

            $this->addFilter($filter);
        }
    }

    /**
     * @param Filter $filter
     * @param string $restrictionField
     * @param array $allowedFieldValues
     * @return bool
     */
    private function isAllowedFilter(Filter $filter, $restrictionField, $allowedFieldValues)
    {
        if ($filter->getField() !== $restrictionField) {
            return true;
        }

        if (in_array($filter->getValue(), $allowedFieldValues)) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(Filter $filter)
    {
        if ($filter->getField() == 'website_id') {
            $this->addMainTablePrefix($filter);
        }

        return parent::addFilter($filter);
    }

    /**
     * @param Filter $filter
     */
    protected function addMainTablePrefix(Filter $filter)
    {
        $fieldName = $filter->getField();
        $filter->setField('main_table.' . $fieldName);
    }
}

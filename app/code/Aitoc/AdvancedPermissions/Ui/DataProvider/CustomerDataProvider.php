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
use Aitoc\AdvancedPermissions\Ui\DataProvider\AddFilterHelper\WebsiteIdsAddFilterHelper;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;

/**
 * Class CustomerDataProvider
 */
class CustomerDataProvider extends DataProvider
{
    /**
     * CustomerDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Reporting $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param AdvancedPermissionHelper $helper
     * @param WebsiteIdsAddFilterHelper $baseAddFilterHelper
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
        WebsiteIdsAddFilterHelper $baseAddFilterHelper,
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
            $helper,
            $baseAddFilterHelper,
            $meta,
            $data
        );
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return SearchCriteria|null
     */
    protected function addAdvancedPermissionFiltersIfRequired(SearchCriteria $searchCriteria)
    {
        return $this->addWebsiteFilterIfRequired($searchCriteria);
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return SearchCriteria|null
     */
    private function addWebsiteFilterIfRequired(SearchCriteria $searchCriteria)
    {
        return $this->addFilterIfRequired($searchCriteria, $this->baseAddFilterHelper);
    }

    public function addFilter(Filter $filter)
    {
        $this->addMainTablePrefixIfRequired($filter);

        return parent::addFilter($filter);
    }

    private function addMainTablePrefixIfRequired(Filter $filter)
    {
        if (!$this->isMainTablePrefixRequired($filter)) {
            return;
        }

        $this->addMainTablePrefix($filter);
    }

    private function isMainTablePrefixRequired(Filter $filter)
    {
        $fieldName = $filter->getField();

        return strpos($fieldName, '.') === false;
    }
}

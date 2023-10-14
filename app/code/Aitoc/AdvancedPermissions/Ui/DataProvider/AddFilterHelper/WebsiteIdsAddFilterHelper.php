<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Ui\DataProvider\AddFilterHelper;
use Aitoc\AdvancedPermissions\Helper\Data as AdvancedPermissionHelper;

/**
 * Class WebsiteIdsAddFilterHelper
 */
class WebsiteIdsAddFilterHelper extends BaseAddFilterHelper
{
    const FIELD_WEBSITE_ID = 'website_id';

    const CUSTOMER_LISTING_DATA_SOURCE = 'customer_listing_data_source';

    public function __construct(AdvancedPermissionHelper $advancedPermissionHelper)
    {
        parent::__construct($advancedPermissionHelper);
    }

    /**
     * @inheritdoc
     */
    protected function getApplicableDatasourceNames()
    {
        return [
            self::CUSTOMER_LISTING_DATA_SOURCE
        ];
    }

    /**
     * @param string $dataProviderName
     * @return bool
     */
    protected function isFilterRequired($dataProviderName)
    {
        return parent::isFilterRequired($dataProviderName)
            && !$this->isCustomerListingWithShowAllEnabled($dataProviderName);
    }

    /**
     * @param $dataProviderName
     * @return bool
     */
    protected function isCustomerListingWithShowAllEnabled($dataProviderName)
    {
        return ($dataProviderName == self::CUSTOMER_LISTING_DATA_SOURCE)
            && $this->helper->getRole()->getShowAllCustomers();
    }

    /**
     * @return string
     */
    protected function getFilterFieldName()
    {
        return $this->getBindedFieldId();
    }

    /**
     * @inheritdoc
     */
    public function getBindedFieldId()
    {
        return self::FIELD_WEBSITE_ID;
    }

    /**
     * @return array
     */
    public function getAllowedFieldIds()
    {
        return $this->getAllowedWebsiteIds();
    }

    protected function getAllowedWebsiteIds()
    {
        return $this->helper->getAllowedWebsiteIds();
    }
}

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

/**
 * Class StoreIdsAddFilterHelper
 */
class StoreIdsAddFilterHelper extends BaseAddFilterHelper
{
    const FIELD_STORE_ID = 'store_id';

    /**
     * @inheritdoc
     */
    protected function getApplicableDatasourceNames()
    {
        return [
            "sales_order_grid_data_source",
            "cms_page_listing_data_source",
            "cms_block_listing_data_source"
        ];
    }

    /**
     * @inheritdoc
     */
    public function getBindedFieldId()
    {
        return self::FIELD_STORE_ID;
    }

    /**
     * @inheritdoc
     */
    protected function getAllowedStoreViewIds()
    {
        return $this->helper->getAllowedStoreViewIds();
    }

    /**
     * @return string
     */
    protected function getFilterFieldName()
    {
        return $this->getBindedFieldId();
    }

    /**
     * @return array
     */
    public function getAllowedFieldIds()
    {
        $allowedStoreIds = $this->getAllowedStoreViewIds();

        if (!$this->helper->isViewAll()) {
            $allowedStoreIds[] = 0;
        }

        return $allowedStoreIds;
    }
}

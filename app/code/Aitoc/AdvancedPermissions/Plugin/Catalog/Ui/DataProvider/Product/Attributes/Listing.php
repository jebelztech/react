<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Plugin\Catalog\Ui\DataProvider\Product\Attributes;

use Aitoc\AdvancedPermissions\Helper\Data;

class Listing
{
    const ATTRIBUTE_ID_NAME = 'attribute_id';

    /**
     * @var Data
     */
    protected $data;

    public function __construct(
        Data $data
    ) {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function afterGetData(
        \Magento\Catalog\Ui\DataProvider\Product\Attributes\Listing $subject,
        $result
    ) {
        $newArray = [];
        $attributes = $this->data->getAttributePermission();
        if (is_array($result) && isset($result['items']) && $this->data->isAdvancedPermissionEnabled()) {
            foreach ($result['items'] as $key => $item) {
                if (isset($item[self::ATTRIBUTE_ID_NAME]) && isset($attributes[$item[self::ATTRIBUTE_ID_NAME]])) {
                    $status = $attributes[$item[self::ATTRIBUTE_ID_NAME]];

                    if ($status != 1) {
                        continue;
                    }
                }

                $newArray[] = $item;
            }

            $result = [
                'totalRecords' => count($newArray),
                'items' => $newArray
            ];
        }

        return $result;
    }
}

<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class ContainerWeightDisabled
 */
class ContainerWeightDisabled implements ModifierInterface
{
    public function modifyData(array $data)
    {
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        if (!isset($meta['product-details']['children']['container_weight'])) {
            return $meta;
        }

        $meta['product-details']['children']['container_weight']['arguments']['data']['config']['component']
            = 'Aitoc_AdvancedPermissions/js/Ui/form/components/container-weight-group';

        return $meta;
    }
}

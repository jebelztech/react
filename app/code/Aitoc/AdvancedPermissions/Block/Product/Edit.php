<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Block\Product;

use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Framework\View\Element\Template\Context;

class Edit extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }



    /**
     * Check if current admin can edit global product attributes, if don't - disable input fields
     *
     * @return bool
     */
    public function isNeedDisable()
    {
        return ($this->helper->isAdvancedPermissionEnabled()
            && !$this->helper->getRole()->getManageGlobalAttribute());
    }


    /**
     * @return mixed
     */
    public function getAllAttributesArray()
    {
        return $this->helper->getAttributePermissionWithName();
    }

    /**
     * Get level scope
     *
     * @return int|null
     */
    public function levelScope()
    {
        return $this->helper->getScope();
    }
}

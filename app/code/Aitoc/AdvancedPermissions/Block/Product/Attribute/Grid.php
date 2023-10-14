<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Block\Product\Attribute;

use Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid;
use Aitoc\AdvancedPermissions\Helper\Data;

class Grid extends \Magento\Catalog\Block\Adminhtml\Product\Attribute\Grid
{
    /**
     * @var Data
     */
    private $helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $collectionFactory, $data);
        $this->helper = $helper;
    }

    /**
     * Process collection after loading
     *
     * @return $this
     */
    protected function _afterLoadCollection()
    {
        $collection = $this->getCollection();
        $items = $collection->getItems();
        $attributes = $this->helper->getAttributePermission();

        if ($items && $this->helper->isAdvancedPermissionEnabled() && $attributes) {
            foreach ($attributes as $key => $attribute) {
                if ($attribute != 1 && array_key_exists($key, $items)) {
                    $collection->removeItemByKey($key);
                }
            }
        }

        $this->setCollection($collection);

        return $this;
    }
}

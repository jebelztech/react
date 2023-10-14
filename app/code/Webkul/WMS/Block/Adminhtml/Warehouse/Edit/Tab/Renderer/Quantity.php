<?php

namespace Webkul\WMS\Block\Adminhtml\Warehouse\Edit\Tab\Renderer;

use \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

/**
 * Qty of product class
 */
class Quantity extends AbstractRenderer
{
    /**
     * Function render
     *
     * @param \Magento\Framework\DataObject $row row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        return $row->getData($this->getColumn()->getIndex());
    }
}

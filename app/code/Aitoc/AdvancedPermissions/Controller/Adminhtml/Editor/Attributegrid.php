<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Controller\Adminhtml\Editor;

use Magento\User\Controller\Adminhtml\User\Role;

class Attributegrid extends Role
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->_initRole();
        $this->_view->loadLayout();
        $grid = $this->_view->getLayout()->createBlock(
            \Aitoc\AdvancedPermissions\Block\Role\Editor\Attribute::class
        )->toHtml();
        $this->getResponse()->setBody($grid);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}

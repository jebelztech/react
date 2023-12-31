<?php
namespace Satish\Manage\Block\Adminhtml\Forms\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('forms_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Forms Information'));
    }
}
<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Block\Role\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;

class Editor extends \Magento\Catalog\Block\Adminhtml\Category\Tree implements TabInterface
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\Backend\Model\Auth\Session $backendSession,
        array $data = []
    ) {
        parent::__construct($context,
            $categoryTree,
            $registry,
            $categoryFactory,
            $jsonEncoder,
            $resourceHelper,
            $backendSession,
            $data

        );
        $this->setTemplate('Aitoc_AdvancedPermissions::role/product/editor.phtml');
    }

    /**
     * Get tab label
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('Advanced Permissions: Product Editing Permissions');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Whether tab is available
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Whether tab is visible
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return $this|\Magento\Catalog\Block\Adminhtml\Category\Tree
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'tabs',
            $this->getLayout()->createBlock(
                \Aitoc\AdvancedPermissions\Block\Role\Editor\Tabs::class,
                'productEditorTabs'
            )
        );
        $this->setChild(
            'attribute',
            $this->getLayout()->createBlock(
                \Aitoc\AdvancedPermissions\Block\Role\Editor\Attribute::class,
                'productEditorAttribute'
            )
        );

        return $this;
    }
}

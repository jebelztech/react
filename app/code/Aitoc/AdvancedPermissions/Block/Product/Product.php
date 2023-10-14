<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Block\Product;

use Aitoc\AdvancedPermissions\Helper\Data;

class Product extends \Magento\Catalog\Block\Adminhtml\Product
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var \Aitoc\AdvancedPermissions\Model\EditorTypeFactory
     */
    private $editorTypeFactory;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Catalog\Model\Product\TypeFactory $typeFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Aitoc\AdvancedPermissions\Model\EditorTypeFactory $editorTypeFactory,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $typeFactory, $productFactory, $data);
        $this->helper = $helper;
        $this->editorTypeFactory = $editorTypeFactory;
    }

    /**
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product
     */
    protected function _prepareLayout()
    {
        $restrictedTypes = $this->getRestrictedProductTypes($this->helper->getRole()->getId());

        if ($this->isNeedDisable() && !$restrictedTypes) {
            $this->toolbar->pushButtons($this, $this->buttonList);

            return $this;
        }

        $addButtonProps = [
            'id' => 'add_new_product',
            'label' => __('Add Product'),
            'class' => 'add',
            'button_class' => '',
            'class_name' => \Magento\Backend\Block\Widget\Button\SplitButton::class,
            'options' => $this->_getAddProductButtonOptions(),
        ];
        $this->buttonList->add('add_new', $addButtonProps);

        return parent::_prepareLayout();
    }

    /**
     * Retrieve options for 'Add Product' split button
     *
     * @return array
     */
    protected function _getAddProductButtonOptions()
    {
        $splitButtonOptions = [];
        $types = $this->_typeFactory->create()->getTypes();
        uasort(
            $types,
            function ($elementOne, $elementTwo) {
                return ($elementOne['sort_order'] < $elementTwo['sort_order']) ? -1 : 1;
            }
        );

        $currentRole = $this->helper->getRole();
        $restrictedTypes = $this->getRestrictedProductTypes($currentRole->getId());

        foreach ($types as $typeId => $type) {
            if ($this->helper->isAdvancedPermissionEnabled()) {
                if ($currentRole->getId()) {
                    if ($restrictedTypes && array_search($typeId, $restrictedTypes) !== false) {
                        $splitButtonOptions[$typeId] = [
                            'label' => __($type['label']),
                            'onclick' => "setLocation('" . $this->_getProductCreateUrl($typeId) . "')",
                            'default' => \Magento\Catalog\Model\Product\Type::DEFAULT_TYPE == $typeId,
                        ];
                    }
                }

                continue;
            } else {
                $splitButtonOptions[$typeId] = [
                    'label' => __($type['label']),
                    'onclick' => "setLocation('" . $this->_getProductCreateUrl($typeId) . "')",
                    'default' => \Magento\Catalog\Model\Product\Type::DEFAULT_TYPE == $typeId,
                ];
            }
        }

        return $splitButtonOptions;
    }

    /**
     * @param $roleId
     * @return mixed
     */
    private function getRestrictedProductTypes($roleId)
    {
        if (!$roleId) {
            return [];
        }

        return $this->editorTypeFactory->create()->getRestrictedTypes($roleId);
    }

    /**
     *
     * @return bool
     */
    public function isNeedDisable()
    {
        return ($this->helper->isAdvancedPermissionEnabled()
            && !$this->helper->getRole()->getCanCreateProducts());
    }

}

<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Block\Role\Tab;


use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Backend\Block\Template\Context as TemplateContext;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\Action\Context as ActionContext;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Data\FormFactory;
use Aitoc\AdvancedPermissions\Model\RoleFactory;
use Aitoc\AdvancedPermissions\Model\EditorTypeFactory;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Create extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{

    /**
     * @var Store
     */
    protected $systemStore;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Yesno
     */
    protected $yesno;

    /**
     * @var Type
     */
    protected $productTypes;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var RoleFactory
     */
    protected $roleFactory;

    /**
     * @var EditorTypeFactory
     */
    protected $editorTypeFactory;

    public function __construct(
        TemplateContext $context,
        ActionContext $contextManager,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        Data $helper,
        Yesno $yesno,
        Type $productTypes,
        RoleFactory $roleFactory,
        EditorTypeFactory $editorTypeFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->objectManager = $contextManager->getObjectManager();
        $this->systemStore = $systemStore;
        $this->helper = $helper;
        $this->yesno = $yesno;
        $this->formFactory  = $formFactory;
        $this->productTypes = $productTypes;
        $this->roleFactory = $roleFactory;
        $this->editorTypeFactory = $editorTypeFactory;
    }

    /**
     * Get tab label
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('Advanced Permissions: Product Creation Permissions');
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

    protected function _prepareForm()
    {
        $form = $this->formFactory->create();


        $fieldset = $form->addFieldset(
            'permissions_product_create',
            ['legend' => __('Product Creation Permissions')]
        );

        $this->_addElementTypes($fieldset);

        $fieldset->addField(
            'allow_create_product',
            'select',
            [
                'name' => 'allow_create_product',
                'label' => __('Limit ability to Create Products'),
                'title' => __('Limit ability to Create Products'),
                'values' => $this->yesno->toOptionArray(),
            ]
        );

        $fieldset->addField(
            'apply_to',
            'multiselect',
            [
                'name' => 'apply_to[]',
                'label' => __('Allow to Create'),
                'values' => $this->productTypes->getOptions(),
                'mode_labels' => [
                    'all' => __('All Product Types'),
                    'custom' => __('Selected Product Types')
                ],
            ],
            'frontend_class'
        );

        $this->setForm($form);
        $this->_setFormValues($form);
    }

    /**
     * @return mixed
     */
    private function getCurrentRole()
    {
        return $this->_coreRegistry->registry('current_role');
    }

    /**
     * @param $form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _setFormValues($form)
    {
        $form->getElement('allow_create_product')->setValue('0');
        $rid = null;
        $role = $this->getCurrentRole();
        $currentRole = $this->objectManager->create(\Aitoc\AdvancedPermissions\Model\Role::class)
            ->loadOriginal($role->getId());

        if ($currentRole->getId()) {
            $rid = $currentRole->getId();
            $advancedrole = $this->roleFactory->create();

            if ($advancedrole->canCreateProducts($rid)) {
                $form->getElement('allow_create_product')->setValue('1');
            }

            $editorType = $this->editorTypeFactory->create();

            if ($applyTo = $editorType->getRestrictedTypes($rid)) {
                $applyTo = is_array($applyTo) ? $applyTo : explode(',', $applyTo);
                $form->getElement('apply_to')->setValue($applyTo);
            }
        } else {
            $form->getElement('allow_create_product')->setValue('1');
        }

        $form->getElement('apply_to')->setSize(5);

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Form\Element\Dependence::class)
                ->addFieldMap("apply_to", 'apply_to')
                ->addFieldMap("allow_create_product", 'allow_create_product')
                ->addFieldDependence('apply_to', 'allow_create_product', '1')
        );
    }

    /**
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return [
            'apply' => \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Apply::class,
        ];
    }
}

<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Block\Role\Editor;

use Magento\Backend\Block\Template\Context as TemplateContext;
use Magento\Framework\Data\FormFactory;
use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Config\Model\Config\Source\Yesno;

class Tabs extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var Yesno
     */
    protected $yesno;

    /**
     * @var \Aitoc\AdvancedPermissions\Model\EditorTabFactory
     */
    protected $editorTabFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Aitoc\AdvancedPermissions\Model\RoleFactory
     */
    private $roleFactory;

    public function __construct(
        TemplateContext $context,
        FormFactory $formFactory,
        Data $helperData,
        Yesno $yesno,
        \Aitoc\AdvancedPermissions\Model\EditorTabFactory $editorTabFactory,
        \Magento\Framework\Registry $registry,
        \Aitoc\AdvancedPermissions\Model\RoleFactory $roleFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->formFactory = $formFactory;
        $this->helperData = $helperData;
        $this->yesno = $yesno;
        $this->editorTabFactory = $editorTabFactory;
        $this->registry = $registry;
        $this->roleFactory = $roleFactory;
    }

    /**
     * @return mixed
     */
    private function getCurrentRole()
    {
        return $this->registry->registry('current_role');
    }

    /**
     * @return \Magento\Backend\Block\Widget\Form|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create();

        $fieldset = $form->addFieldset('permissions_product_editor', []);
        $tabs     = $this->helperData->getProductTabs();

        foreach ($tabs as $name => $title) {
            $fieldset->addField(
                $name,
                'checkbox',
                array(
                    'name' => $name,
                    'label' => __($title),
                    'title' => __($title),
                    'values' => $this->yesno->toOptionArray(),
                )
            );
        }

        $this->setForm($form);
        $this->_setFormValues($form);
    }


    /**
     * @param $form
     */
    protected function _setFormValues($form)
    {
        $rid     = null;
        $role = $this->getCurrentRole();
        $currentRole = $this->roleFactory->create()->loadOriginal($role->getId());

        if ($currentRole->getId()) {
            $rid = $currentRole->getId();

            $editorTab    = $this->editorTabFactory->create();
            $disabledTabs = $editorTab->getDisabledTabs($rid);

            if ($disabledTabs) {
                foreach ($disabledTabs as $tab) {
                    if ($form->getElement($tab)) {
                        $form->getElement($tab)->setChecked(1);
                    }
                }
            }
        }
    }
}

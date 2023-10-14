<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Block\Role\Tab;

use Aitoc\AdvancedPermissions\Helper\Data;
use Aitoc\AdvancedPermissions\Model\Role;
use Aitoc\AdvancedPermissions\Model\Stores;
use Magento\Backend\Block\Template\Context as TemplateContext;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\App\Action\Context as ActionContext;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Store\Model\Group;
use Magento\Store\Model\System\Store;

class Settings extends Generic implements TabInterface
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
     * @var Role
     */
    protected $role;

    /**
     * @var Stores
     */
    protected $stores;

    /**
     * @var Group
     */
    protected $group;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var
     */
    protected $roleManager;

    /**
     * Settings constructor.
     *
     * @param TemplateContext $context
     * @param Registry $registry
     * @param ActionContext $contextManager
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param Data $helper
     * @param Role $roleGen
     * @param Stores $stores
     * @param Group $group
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        Registry $registry,
        ActionContext $contextManager,
        FormFactory $formFactory,
        Store $systemStore,
        Data $helper,
        \Aitoc\AdvancedPermissions\Model\Role $roleGen,
        Stores $stores,
        Group $group,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_objectManager = $contextManager->getObjectManager();
        $this->systemStore = $systemStore;
        $this->helper = $helper;
        $this->role = $roleGen;
        $this->stores = $stores;
        $this->group = $group;
    }

    /**
     * Get tab label
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('Advanced Permissions: Settings');
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
     * Is single store
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }

    /**
     * Get role from registry
     *
     * @return mixed
     */
    public function getRole()
    {
        if (!$this->roleManager) {
            $role = $this->_coreRegistry->registry('current_role');
            $this->roleManager = $this->_objectManager->create(Role::class)->loadOriginal($role->getId());
        }

        return $this->roleManager;
    }

    /**
     * get suffix
     *
     * @return string
     */
    public function getSuffix()
    {
        return 'setting';
    }

    /**
     * Get field from role
     *
     * @param $field
     *
     * @return mixed
     */
    public function getFieldValue($field)
    {
        return $this->getRole()->getParam($field);
    }

    /**
     * Get field from Global
     *
     * @param $field
     *
     * @return int
     */
    public function getFieldValueUseConfig($field)
    {
        if ($this->getRole()->hasData($field)) {
            return $this->getRole()->getData($field);
        }

        return 1;
    }
}

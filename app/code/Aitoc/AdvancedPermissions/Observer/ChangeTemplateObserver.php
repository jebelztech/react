<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Module\Manager as ModuleManager;

/**
 * Class ChangeTemplateObserver
 */
class ChangeTemplateObserver implements ObserverInterface
{
    const MODULE_NAME_PRODUCT_VIDEO = 'Magento_ProductVideo';
    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * ChangeTemplateObserver constructor.
     * @param ModuleManager $moduleManager
     */
    public function __construct(ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $templatePath = $this->getTemplate();
        $this->setTemplate($observer, $templatePath);
    }

    /**
     * @return string
     */
    private function getTemplate()
    {
        $templateName = $this->isModuleProductVideoEnabled()
            ? 'gallery-video.phtml'
            : 'gallery.phtml';

        return 'Aitoc_AdvancedPermissions::helper/' . $templateName;
    }

    /**
     * @return bool
     */
    private function isModuleProductVideoEnabled()
    {
        return $this->moduleManager->isEnabled(self::MODULE_NAME_PRODUCT_VIDEO);
    }

    /**
     * @param Observer $observer
     * @param string $templatePath
     */
    private function setTemplate($observer, $templatePath)
    {
        $observer->getBlock()->setTemplate($templatePath);
    }
}

<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Ui\DataProvider\Product\Form\Modifier;

use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Store\Api\GroupRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Websites
 *
 * @package Aitoc\AdvancedPermissions\Ui\DataProvider\Product\Form\Modifier
 */
class Websites extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Websites
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Websites constructor.
     *
     * @param LocatorInterface $locator
     * @param StoreManagerInterface $storeManager
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param GroupRepositoryInterface $groupRepository
     * @param StoreRepositoryInterface $storeRepository
     * @param Data $helper
     */
    public function __construct(
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        WebsiteRepositoryInterface $websiteRepository,
        GroupRepositoryInterface $groupRepository,
        StoreRepositoryInterface $storeRepository,
        Data $helper
    ) {
        parent::__construct($locator, $storeManager, $websiteRepository, $groupRepository, $storeRepository);
        $this->helper = $helper;
    }

    /**
     * Prepares websites list with groups and stores as array
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function getWebsitesList()
    {
        parent::getWebsitesList();

        if ($this->helper->isAdvancedPermissionEnabled()) {
            $websites = $this->helper->getAllowedWebsiteIds();
            foreach ($this->websitesList as $key => $item) {
                if (!in_array($item['id'], $websites)) {
                    unset($this->websitesList[$key]);
                }
            }
        }

        return $this->websitesList;
    }
}

<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Block\Product\Helper\Form;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Catalog\Api\Data\ProductInterface;
use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Eav\Model\Config;

/**
 * Adminhtml gallery block
 */
class Gallery extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery
{
    const ATTRIBUTE_MEDIA_GALLERY_NAME = 'media_gallery';

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Registry $registry,
        \Magento\Framework\Data\Form $form,
        Data $helper,
        Config $eavConfig,
        $data = [],
        DataPersistorInterface $dataPersistor = null
    ) {
        parent::__construct($context, $storeManager, $registry, $form, $data, $dataPersistor);
        $this->dataPersistor = $dataPersistor ?: ObjectManager::getInstance()->get(DataPersistorInterface::class);
        $this->helper = $helper;
        $this->eavConfig = $eavConfig;
    }

    /**
     * Returns element html.
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = $this->getContentHtml();
        return $html;
    }

    /**
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getReadonly()
    {
        $attributeDetails = $this->eavConfig->getAttribute('catalog_product', self::ATTRIBUTE_MEDIA_GALLERY_NAME);
        $attributeData    = $attributeDetails->getData();
        $attrId            = $attributeData['attribute_id'];

        $attributePermissionArray = $this->helper->getAttributePermission();

        if (isset($attributePermissionArray[$attrId])) {
            if ($attributePermissionArray[$attrId] == 0) {
                return true;
            } else {
                if ($attributePermissionArray[$attrId] == 2) {
                    return true;
                }
            }

            return false;
        }

        return false;
    }
}

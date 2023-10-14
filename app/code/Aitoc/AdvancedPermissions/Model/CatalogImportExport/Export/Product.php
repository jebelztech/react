<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Model\CatalogImportExport\Export;

use Aitoc\AdvancedPermissions\Helper\Data as AdvancedPermissionHelper;
use Magento\Catalog\Model\Product\LinkTypeProvider;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as ProductAttributeCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory as ProductOptionCollectionFactory;
use Magento\Catalog\Model\ResourceModel\ProductFactory;
use Magento\CatalogImportExport\Model\Export\Product as CoreExportProduct;
use Magento\CatalogImportExport\Model\Export\Product\Type\Factory as ExportProductTypeFactory;
use Magento\CatalogImportExport\Model\Export\RowCustomizerInterface;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Magento\CatalogInventory\Model\ResourceModel\Stock\ItemFactory;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as EntityAttributeSetCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\ImportExport\Model\Export\ConfigInterface;
use Magento\ImportExport\Model\Import;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class Product
 */
class Product extends CoreExportProduct
{
    const VERSION_WHERE_DATE_ATTR_CODES_ADDED = '2.1.3';

    /**
     * @var AdvancedPermissionHelper
     */
    private $advancedPermissionHelper;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadataInterface;

    /**
     * Product constructor.
     * @param TimezoneInterface $localeDate
     * @param Config $config
     * @param ResourceConnection $resource
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     * @param ConfigInterface $exportConfig
     * @param ProductFactory $productFactory
     * @param EntityAttributeSetCollectionFactory $attrSetColFactory
     * @param CategoryCollectionFactory $categoryColFactory
     * @param ItemFactory $itemFactory
     * @param ProductOptionCollectionFactory $optionColFactory
     * @param ProductAttributeCollectionFactory $attributeColFactory
     * @param ExportProductTypeFactory $_typeFactory
     * @param LinkTypeProvider $linkTypeProvider
     * @param RowCustomizerInterface $rowCustomizer
     * @param AdvancedPermissionHelper $advancedPermissionHelper
     * @param ProductMetadataInterface $productMetadataInterface
     * @param array $dateAttrCodes
     */
    public function __construct(
        TimezoneInterface $localeDate,
        Config $config,
        ResourceConnection $resource,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        ConfigInterface $exportConfig,
        ProductFactory $productFactory,
        EntityAttributeSetCollectionFactory $attrSetColFactory,
        CategoryCollectionFactory $categoryColFactory,
        ItemFactory $itemFactory,
        ProductOptionCollectionFactory $optionColFactory,
        ProductAttributeCollectionFactory $attributeColFactory,
        ExportProductTypeFactory $_typeFactory,
        LinkTypeProvider $linkTypeProvider,
        RowCustomizerInterface $rowCustomizer,
        AdvancedPermissionHelper $advancedPermissionHelper,
        ProductMetadataInterface $productMetadataInterface,
        array $dateAttrCodes = []
    ) {
        $this->advancedPermissionHelper = $advancedPermissionHelper;
        $this->productMetadataInterface = $productMetadataInterface;

        $currentMagentoVersion = $this->getCurrentMagentoVersion();

        version_compare($currentMagentoVersion, self::VERSION_WHERE_DATE_ATTR_CODES_ADDED, '<')
            ? parent::__construct(
                $localeDate,
                $config,
                $resource,
                $storeManager,
                $logger,
                $collectionFactory,
                $exportConfig,
                $productFactory,
                $attrSetColFactory,
                $categoryColFactory,
                $itemFactory,
                $optionColFactory,
                $attributeColFactory,
                $_typeFactory,
                $linkTypeProvider,
                $rowCustomizer
            )
            : parent::__construct(
                $localeDate,
                $config,
                $resource,
                $storeManager,
                $logger,
                $collectionFactory,
                $exportConfig,
                $productFactory,
                $attrSetColFactory,
                $categoryColFactory,
                $itemFactory,
                $optionColFactory,
                $attributeColFactory,
                $_typeFactory,
                $linkTypeProvider,
                $rowCustomizer,
                $dateAttrCodes
            )
        ;
    }

    /**
     * @return string
     */
    private function getCurrentMagentoVersion()
    {
        return $this->productMetadataInterface->getVersion();
    }

    /**
     * @inheritdoc
     */
    protected function getExportData()
    {
        return $this->isAdvancedPermissionEnabled()
            ? $this->getExportDataOverridden()
            : parent::getExportData();
    }

    /**
     * @return bool
     */
    private function isAdvancedPermissionEnabled()
    {
        return $this->advancedPermissionHelper->isAdvancedPermissionEnabled();
    }

    /**
     * Get export data for collection.
     *
     * Fill copy-paste form core class for using overloaded private appendMultirowData().
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    private function getExportDataOverridden()
    {
        $exportData = [];

        try {
            $rawData = $this->collectRawData();
            $multirawData = $this->collectMultirawData();

            $productIds = array_keys($rawData);
            $stockItemRows = $this->prepareCatalogInventory($productIds);

            $this->rowCustomizer->prepareData(
                $this->_prepareEntityCollection($this->_entityCollectionFactory->create()),
                $productIds
            );

            $this->setHeaderColumns($multirawData['customOptionsData'], $stockItemRows);

            foreach ($rawData as $productId => $productData) {
                foreach ($productData as $storeId => $dataRow) {
                    if (isset($stockItemRows[$productId])) {
                        $dataRow = array_merge($dataRow, $stockItemRows[$productId]);
                    }
                    $this->appendMultirowData($dataRow, $multirawData);
                    if ($dataRow) {
                        $exportData[] = $dataRow;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }
        return $exportData;
    }

    /**
     * Copy-paste from original method except to removed `if (Store::DEFAULT_STORE_ID == $storeId) {` condition;
     *
     * @param array $dataRow
     * @param array $multiRawData
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function appendMultirowData(&$dataRow, &$multiRawData)
    {
        $productId = $dataRow['product_id'];
        $productLinkId = $dataRow['product_link_id'];
        $storeId = $dataRow['store_id'];
        $sku = $dataRow[self::COL_SKU];

        unset($dataRow['product_id']);
        unset($dataRow['product_link_id']);
        unset($dataRow['store_id']);
        unset($dataRow[self::COL_SKU]);

        unset($dataRow[self::COL_STORE]);
        $this->updateDataWithCategoryColumns($dataRow, $multiRawData['rowCategories'], $productId);
        if (!empty($multiRawData['rowWebsites'][$productId])) {
            $websiteCodes = [];
            foreach ($multiRawData['rowWebsites'][$productId] as $productWebsite) {
                $websiteCodes[] = $this->_websiteIdToCode[$productWebsite];
            }
            $dataRow[self::COL_PRODUCT_WEBSITES] =
                implode(Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR, $websiteCodes);
            $multiRawData['rowWebsites'][$productId] = [];
        }
        if (!empty($multiRawData['mediaGalery'][$productLinkId])) {
            $additionalImages = [];
            $additionalImageLabels = [];
            $additionalImageIsDisabled = [];
            foreach ($multiRawData['mediaGalery'][$productLinkId] as $mediaItem) {
                $additionalImages[] = $mediaItem['_media_image'];
                $additionalImageLabels[] = $mediaItem['_media_label'];

                if ($mediaItem['_media_is_disabled'] == true) {
                    $additionalImageIsDisabled[] = $mediaItem['_media_image'];
                }
            }
            $dataRow['additional_images'] =
                implode(Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR, $additionalImages);
            $dataRow['additional_image_labels'] =
                implode(Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR, $additionalImageLabels);
            $dataRow['hide_from_product_page'] =
                implode(Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR, $additionalImageIsDisabled);
            $multiRawData['mediaGalery'][$productLinkId] = [];
        }
        foreach ($this->_linkTypeProvider->getLinkTypes() as $linkTypeName => $linkId) {
            if (!empty($multiRawData['linksRows'][$productLinkId][$linkId])) {
                $colPrefix = $linkTypeName . '_';

                $associations = [];
                foreach ($multiRawData['linksRows'][$productLinkId][$linkId] as $linkData) {
                    if ($linkData['default_qty'] !== null) {
                        $skuItem = $linkData['sku'] . ImportProduct::PAIR_NAME_VALUE_SEPARATOR .
                            $linkData['default_qty'];
                    } else {
                        $skuItem = $linkData['sku'];
                    }
                    $associations[$skuItem] = $linkData['position'];
                }
                $multiRawData['linksRows'][$productLinkId][$linkId] = [];
                asort($associations);
                $dataRow[$colPrefix . 'skus'] =
                    implode(Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR, array_keys($associations));
                $dataRow[$colPrefix . 'position'] =
                    implode(Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR, array_values($associations));
            }
        }

        $dataRow = $this->rowCustomizer->addData($dataRow, $productId);

        if (!empty($this->collectedMultiselectsData[$storeId][$productId])) {
            foreach (array_keys($this->collectedMultiselectsData[$storeId][$productId]) as $attrKey) {
                if (!empty($this->collectedMultiselectsData[$storeId][$productId][$attrKey])) {
                    $dataRow[$attrKey] = implode(
                        Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR,
                        $this->collectedMultiselectsData[$storeId][$productId][$attrKey]
                    );
                }
            }
        }

        if (!empty($multiRawData['customOptionsData'][$productLinkId][$storeId])) {
            $customOptionsRows = $multiRawData['customOptionsData'][$productLinkId][$storeId];
            $multiRawData['customOptionsData'][$productLinkId][$storeId] = [];
            $customOptions = implode(ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR, $customOptionsRows);

            $dataRow = array_merge($dataRow, ['custom_options' => $customOptions]);
        }

        if (empty($dataRow)) {
            return null;
        } elseif ($storeId != Store::DEFAULT_STORE_ID) {
            $dataRow[self::COL_STORE] = $this->_storeIdToCode[$storeId];
        }
        $dataRow[self::COL_SKU] = $sku;
        return $dataRow;
    }

    /**
     * @inheritdoc
     */
    protected function initCategories()
    {
        $collection = $this->_categoryColFactory->create()->addNameToResult();
        $filteredByRestrictionCollection = $this->_categoryColFactory->create()->addNameToResult();

        if ($this->isRestrictedByCategories()) {
            $this->addCategoryIdFilterToCategoryCollection($filteredByRestrictionCollection);
        }

        /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
        foreach ($filteredByRestrictionCollection as $category) {
            $structure = preg_split('#/+#', $category->getPath());
            $pathSize = count($structure);

            if ($pathSize > 1) {
                $path = [];

                for ($i = 1; $i < $pathSize; $i++) {
                    $path[] = $collection->getItemById($structure[$i])->getName();
                }

                $this->_rootCategories[$category->getId()] = array_shift($path);

                if ($pathSize > 2) {
                    $this->_categories[$category->getId()] = implode('/', $path);
                }
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareEntityCollection(\Magento\Eav\Model\Entity\Collection\AbstractCollection $collection)
    {
        $collection = parent::_prepareEntityCollection($collection);

        if (!$this->isRestrictedByCategories()) {
            return $collection;
        }

        $this->addCategoryIdFilterToProductCollection($collection);

        return $collection;
    }

    /**
     * @param ProductCollection $collection
     */
    private function addCategoryIdFilterToProductCollection(ProductCollection $collection)
    {
        $categoriesFilter = ['eq' => $this->getFilteredCategoryIds()];
        $collection->addCategoriesFilter($categoriesFilter);
    }

    /**
     * @return bool
     */
    private function isRestrictedByCategories()
    {
        if ($this->advancedPermissionHelper->getScope() != AdvancedPermissionHelper::SCOPE_STORE) {
            return false;
        }

        $allowedCategories = $this->advancedPermissionHelper->getCategoryIds();

        if (!$allowedCategories) {
            return false;
        }

        return true;
    }

    /**
     * @param CategoryCollection $categoryCollection
     */
    private function addCategoryIdFilterToCategoryCollection(CategoryCollection $categoryCollection)
    {
        $categoryIds = $this->getFilteredCategoryIds();
        $categoryCollection->addIdFilter($categoryIds);
    }

    /**
     * @return array
     */
    private function getFilteredCategoryIds()
    {
        return $this->advancedPermissionHelper->getCategoryIds();
    }
}

<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Plugin\Ui\DataProvider\Product;

use Aitoc\AdvancedPermissions\Helper\Data;
use Closure;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider as CoreProductDataProvider;
use Magento\Framework\App\ProductMetadataInterface;

class ProductDataProvider
{
    const CATEGORIES_TYPE_NOT_NULL = 0;
    const CATEGORIES_TYPE_STRICT = 1;
    const CATEGORIES_TYPE_IS_NULL = 2;
    const CATEGORIES_TYPE_ALL_AND_NULL = 3;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    private $checkedExts = [
        'Magento_InventoryCatalogApi',
        'Magento_InventoryApi',
        'Magento_InventoryConfigurationApi',
        'Magento_InventoryConfigurationApi',
    ];


    /**
     * ProductDataProvider constructor.
     *
     * @param Data $helper
     * @param Collection $collection
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        Data $helper,
        Collection $collection,
        CategoryFactory $categoryFactory,
        ProductMetadataInterface $productMetadata,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->helper = $helper;
        $this->collection = $collection;
        $this->categoryFactory = $categoryFactory;
        $this->productMetadata = $productMetadata;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param CoreProductDataProvider $object
     * @param Closure $work
     *
     * @return array
     */
    public function aroundGetData(CoreProductDataProvider $object, Closure $work)
    {
        $collection = $object->getCollection();
        $categoryIdType = 0;
        if ($this->helper->isAdvancedPermissionEnabled()) {
            if (is_array($this->helper->getCategoryIds()) &&
                count($this->helper->getCategoryIds()) > 0 &&
                $this->helper->getScope() == Data::SCOPE_STORE
            ) {
                $categoryIdType = self::CATEGORIES_TYPE_STRICT;
            }
            if ($categoryIdType && $this->helper->getRole()->getAllowNullCategory()) {
                $categoryIdType = self::CATEGORIES_TYPE_IS_NULL;
            }
            if (!$categoryIdType && $this->helper->getRole()->getAllowNullCategory()) {
                $categoryIdType = self::CATEGORIES_TYPE_ALL_AND_NULL;
            }
        }
        $categories = $this->getFullCategories($this->helper->getCategoryIds());

        if (!$object->getCollection()->isLoaded()) {
            if ($this->helper->isAdvancedPermissionEnabled()) {
                switch ($categoryIdType) {
                    case self::CATEGORIES_TYPE_STRICT:
                        $this->addCategoriesFilter($collection, 'in', 'in', $categories);
                        break;
                        
                    case self::CATEGORIES_TYPE_ALL_AND_NULL:
                        // any product?
                        break;
                        
                    case self::CATEGORIES_TYPE_IS_NULL:
                        // catalog_category_product has not records with NULL category_id
                        // so we need just exclude items that not in $categories
                        $this->addCategoriesFilter($collection, 'nin', 'nin', $categories);
                        break;

                    case self::CATEGORIES_TYPE_NOT_NULL:
                        $this->addCategoriesFilter($collection, 'in', 'notnull', true);
                        break;
                }
            }
            $collection->load();
        }
        if ($this->helper->isAdvancedPermissionEnabled()) {
            $total = $collection->getConnection()->fetchOne($this->getCountCollection($collection), []);
        } else {
            $total = $collection->getSize();
        }

        $items = $collection->toArray();

        if (version_compare($this->productMetadata->getVersion(), '2.3.0', '>=')) {
            $extEnabled = true;
            foreach ($this->checkedExts as $checkedExt) {
                if (!$this->moduleManager->isEnabled($checkedExt)) {
                    $extEnabled = false;
                    break;
                }
            }

            if ($extEnabled) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $quantityPerSourceModifier = $objectManager
                    ->get('Magento\InventoryCatalogAdminUi\Ui\DataProvider\Product\Listing\Modifier\QuantityPerSource');
                $data['totalRecords'] = $total;
                $data['items'] = $items;
                $items = $quantityPerSourceModifier->modifyData($data)['items'];
            }
        }


        return [
            'totalRecords' => $total,
            'items' => array_values($items),
        ];
    }

    /**
     * Filter Product by Categories
     */
    public function addCategoriesFilter($collection, $productCondition, $categoryCondition, $values)
    {
        $categorySelect = $collection->getConnection()->select()->from(
            ['cat' => $collection->getTable('catalog_category_product')],
            'cat.product_id'
        )->where($collection->getConnection()->prepareSqlCondition('cat.category_id', [$categoryCondition => $values]));
        
        $collection->getSelect()->where(
            $collection->getConnection()->prepareSqlCondition(
                'e.entity_id',
                [$productCondition => $categorySelect]
            )
        );
    }
    
    /**
     * Get Count records with option
     *
     * @param $collection
     *
     * @return mixed
     */
    public function getCountCollection($collection)
    {
        $collectionClone = clone $collection->getSelect();
        $collectionClone->reset(\Magento\Framework\DB\Select::ORDER);
        $collectionClone->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $collectionClone->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $collectionClone->reset(\Magento\Framework\DB\Select::GROUP);
        $collectionClone->reset(\Magento\Framework\DB\Select::COLUMNS);
        $collectionClone->columns('COUNT(DISTINCT(e.entity_id))');

        return $collectionClone;
    }

    /**
     * Get All Categories
     *
     * @param $elements
     *
     * @return array
     */
    protected function getFullCategories($elements)
    {
        $categories = [];
        foreach ($elements as $element) {
            $category = $this->categoryFactory->create()->load($element);
            $children = $category->getChildren();
            $childs = explode(',', $children);
            $categories = array_merge($categories, array_diff($childs, $categories));
        }
        if (count($categories)) {
            $elements = array_merge($elements, array_diff($categories, $elements));
        }
        foreach ($elements as $key => $value) {
            if (!$value) {
                unset($elements[$key]);
            }
        }

        return $elements;
    }
}

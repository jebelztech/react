<?php
/**
 * Webkul Software.
 *
 * PHP version 7.0+
 *
 * @category  Webkul
 * @package   Webkul_WMS
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

namespace Webkul\WMS\Block\Adminhtml\Warehouse\Edit\Tab;

use Magento\Inventory\Model\ResourceModel\SourceItem\CollectionFactory;

/**
 * Class ProductGrid for locations
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class ProductGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Type
     *
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $type;

    /**
     * ResourceConnection
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * WarehouseFactory
     *
     * @var \Webkul\WMS\Model\WarehouseFactory
     */
    protected $warehouse;

    /**
     * Visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $visibility;

    /**
     * CollectionFactory
     *
     * @var CollectionFactory
     */
    protected $sourceItem;

    /**
     * Manager
     *
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * ProductFactory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * Constructor
     *
     * @param CollectionFactory                         $sourceItem      sourceItem
     * @param \Magento\Catalog\Model\Product\Type       $type            type
     * @param \Magento\Backend\Helper\Data              $backendHelper   backendHelper
     * @param \Webkul\WMS\Model\WarehouseFactory        $warehouse       warehouse
     * @param \Magento\Framework\Module\Manager         $moduleManager   moduleManager
     * @param \Magento\Backend\Block\Template\Context   $context         context
     * @param \Magento\Framework\App\ResourceConnection $resource        resource
     * @param \Magento\Catalog\Model\ProductFactory     $productFactory  productFactory
     * @param \Magento\Catalog\Model\Product\Visibility $visibility      visibility
     * @param array                                     $data            data
     */
    public function __construct(
        CollectionFactory $sourceItem,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Backend\Helper\Data $backendHelper,
        \Webkul\WMS\Model\WarehouseFactory $warehouse,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        array $data = []
    ) {
        $this->type = $type;
        $this->resource = $resource;
        $this->warehouse = $warehouse;
        $this->visibility = $visibility;
        $this->sourceItem = $sourceItem;
        $this->moduleManager = $moduleManager;
        $this->productFactory = $productFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Contructor method
     *
     * @return null
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
        $this->setDefaultDir("ASC");
        $this->setDefaultSort("entity_id");
        $this->setId("warehouse_product_grid");
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare Collection method
     *
     * @return grid
     */
    protected function _prepareCollection()
    {
        $id = $this->getRequest()->getParam("id");
        $LT = $this->resource->getTableName("wms_product_location");
        $collection = $this->productFactory->create()
            ->getCollection()
            ->addAttributeToSelect("*")
            ->addFieldToFilter("entity_id", 0);
        if ($id) {
            $warehouse = $this->warehouse->create()->load($id);
            $sourceCollection = $this->sourceItem->create()
                ->addFieldToSelect("sku")
                ->addFieldToSelect("source_code")
                ->addFieldToFilter("source_code", $warehouse->getSource());
            $skus = [];
            foreach ($sourceCollection as $each) {
                $skus[] = $each->getSku();
            }
            $collection = $this->productFactory->create()
                ->getCollection()
                ->addAttributeToSelect("*");
            $joinConditions = 'sourceTable.sku = e.sku';
            $collection->getSelect()->join(
                ['sourceTable' => $collection->getTable('inventory_source_item')],
                $joinConditions,
                ["quantity" => "sourceTable.quantity", "source_code" => "sourceTable.source_code"]
            )->where("sourceTable.source_code = '".$warehouse->getSource()."'");
            $collection->addFieldToFilter("sku", ["in" => $skus]);
            $select = $collection->getSelect();
            $sql = $select->__toString();
            $select->reset();
            $select->from(['e' => new \Zend_Db_Expr("($sql)")]);
            $select->reset(\Magento\Framework\DB\Select::COLUMNS);
            $select->columns(
                new \Zend_Db_Expr(
                    "e.*,
                    IFNULL(
                        (
                            SELECT (
                                GROUP_CONCAT(CONCAT(
                                    'Row:', LT.row, ' Col:', LT.column, ' Slv:', LT.shelf, ' Rack:', LT.rack,
                                     ' Qty : ', LT.location_qty
                                ) SEPARATOR '<br>')
                            )
                            FROM $LT AS LT
                            WHERE e.entity_id = LT.product_id AND
                                  LT.warehouse_id = $id
                            GROUP BY LT.product_id
                        ),
                        'Not Assigned'
                    ) as location"
                )
            );
        } else {
            $joinConditions = 'sourceTable.sku = e.sku';
            $collection->getSelect()->join(
                ['sourceTable' => $collection->getTable('inventory_source_item')],
                $joinConditions,
                ["quantity"]
            );
        }
        
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare column method
     *
     * @return column
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            "entity_id",
            [
                "type" => "number",
                "align" => "center",
                "width" => "30px",
                "index" => "entity_id",
                "header" => __("ID")
            ]
        );

        $this->addColumn(
            "thumbnail",
            [
                "type" => "image",
                "align" => "center",
                "index" => "thumbnail",
                "header" => __("Thumbnail"),
                "escape" => true,
                "filter" => false,
                "renderer" => \Webkul\WMS\Block\Adminhtml\Thumbnail::class,
                "sortable" => false
            ]
        );

        $this->addColumn(
            "name",
            [
                "index" => "name",
                "align" => "left",
                "header" => __("Product Name")
            ]
        );

        $this->addColumn(
            "sku",
            [
                "index" => "sku",
                "align" => "left",
                "header" => __("Product Sku")
            ]
        );

        $this->addColumn(
            "quantity",
            [
                "index" => "quantity",
                "align" => "left",
                "header" => __("Product Qty"),
                "filter" => false,
                "renderer" => \Webkul\WMS\Block\Adminhtml\Warehouse\Edit\Tab\Renderer\Quantity::class,
                "sortable" => false
            ]
        );

        $this->addColumn(
            "type",
            [
                "header" => __("Type"),
                "index" => "type_id",
                "type" => "options",
                "options" => $this->type->getOptionArray(),
                "header_css_class" => "col-type",
                "column_css_class" => "col-type productType"
            ]
        );

        $this->addColumn(
            "location",
            [
                "filter" => false,
                "sortable" => false,
                "type" => "wrapline",
                "index" => "location",
                "header" => __("Location")
            ]
        );

        $this->addColumn(
            "action",
            [
                "width" => "80",
                "type" => "action",
                "header" => __("Assign"),
                "actions" => [
                    [
                        "caption" => __("Assign")
                    ]
                ],
                "filter" => false,
                "sortable" => false,
                "is_system" => true,
                "index" => "entity_id"
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Gets grid url for filter
     *
     * @return gridurl
     */
    public function getGridUrl()
    {
        return $this->getUrl("*/*/productGridData", ["_current"=>true]);
    }
}

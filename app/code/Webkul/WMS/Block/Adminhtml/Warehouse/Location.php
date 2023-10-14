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

namespace Webkul\WMS\Block\Adminhtml\Warehouse;

use Webkul\WMS\Model\ResourceModel\ProductLocation\CollectionFactory;

/**
 * Class Assignment
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Location extends \Magento\Backend\Block\Template
{
    protected $locationData = null;
    /**
     * Data
     *
     * @var \Webkul\WMS\Helper\Data
     */
    public $helper;

    /**
     * RequestContentInterface
     *
     * @var \Magento\Framework\App\RequestContentInterface
     */
    public $request;

    /**
     * ResourceConnection
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * Attribute
     *
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $eavAttribute;

    /**
     * RequestContentInterface
     *
     * @var \Magento\Framework\App\RequestContentInterface
     */
    protected $locationFactory;

    /**
     * WarehouseRepositoryInterface
     *
     * @var \Webkul\WMS\Api\WarehouseRepositoryInterface
     */
    protected $warehouseRepository;

    /**
     * Json helper data
     *
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * Constructor
     *
     * @param \Webkul\WMS\Helper\Data $helper
     * @param CollectionFactory $locationFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Eav\Model\Entity\Attribute $eavAttribute
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\App\RequestContentInterface $request
     * @param \Webkul\WMS\Api\WarehouseRepositoryInterface $warehouseRepository
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param array $data
     */
    public function __construct(
        \Webkul\WMS\Helper\Data $helper,
        CollectionFactory $locationFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Eav\Model\Entity\Attribute $eavAttribute,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\RequestContentInterface $request,
        \Webkul\WMS\Api\WarehouseRepositoryInterface $warehouseRepository,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->jsonHelper = $jsonHelper;
        $this->request = $request;
        $this->resource = $resource;
        $this->eavAttribute = $eavAttribute;
        $this->locationFactory = $locationFactory;
        $this->warehouseRepository = $warehouseRepository;
        parent::__construct($context, $data);
    }

    /**
     * Function getHelper
     *
     * @return Data
     */
    public function getLocationTotalQty()
    {
        $qtyCount = 0;
        $locationArray = $this->getLocationArray();
        foreach ($locationArray as $location) {
            if (isset($location["location_qty"])) {
                $qtyCount =  $qtyCount + $location["location_qty"];
            }

        }
        return $qtyCount;
    }

    public function getTotalEmptyLocations()
    {
        $warehouse = $this->getCurrentWarehouse();
        $toteCount = $warehouse->getToteCount();
        $totalAssignedQty = $this->getLocationTotalQty();
        return ($toteCount - $totalAssignedQty);
    }

    /**
     * Function getHelper
     *
     * @return Data
     */
    public function getLocationJson()
    {
        return $this->jsonHelper->jsonEncode($this->getLocationArray());
    }

    /**
     * Function getHelper
     *
     * @return Data
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * Function getCurrentWarehouse
     *
     * @return warehouse
     */
    public function getCurrentWarehouse()
    {
        $warehouseId = (int)$this->request->getParam("id");
        return $this->warehouseRepository->getById($warehouseId);
    }

    /**
     * Function getLocationArray
     *
     * @return array
     */
    public function getLocationArray()
    {
        if ($this->locationData != null) {
            return $this->locationData;
        }
        $warehouseId = (int)$this->request->getParam("id");
        $collection = $this->locationFactory->create()
            ->addFieldToFilter("warehouse_id", $warehouseId);
        $nameAttributeId = $this->eavAttribute
            ->loadByCode("catalog_product", "name")
            ->getAttributeId();
        $cpev = $this->resource->getTableName("catalog_product_entity_varchar");
        $where = "main_table.product_id=cpev.entity_id AND store_id=0 AND cpev.attribute_id=".$nameAttributeId;
        $collection->getSelect()->joinLeft(
            [
                "cpev" => $cpev
            ],
            $where,
            [
                "name" => "value"
            ]
        );
        $data = $collection->toArray();
        $data = $data["items"];
        foreach ($data as $key => $each) {
            if ($each["product_id"] != 0) {
                $data[$key]["url"] = $this->getUrl(
                    "catalog/product/edit",
                    [
                        "_current" => true,
                        "id" => $each["product_id"]
                    ]
                );
            } else {
                $data[$key]["url"] = "";
            }
        }
        return $this->locationData = $data;
    }

    /**
     * Get AssignLocationUrl
     *
     * @return string
     */
    public function getAssignLocationUrl()
    {
        return $this->getUrl(
            "wms/warehouse/assignLocation",
            [
                "_current" => true
            ]
        );
    }
}

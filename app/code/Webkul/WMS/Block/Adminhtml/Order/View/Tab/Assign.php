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

namespace Webkul\WMS\Block\Adminhtml\Order\View\Tab;

/**
 * Class Assign order
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Assign extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * FormKey
     *
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * ResourceConnection
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * Option factory
     *
     * @var \Magento\Catalog\Model\Product\OptionFactory
     */
    protected $optionFactory;

    /**
     * String
     *
     * @var string
     */
    protected $truncateResult;

    /**
     * CatalogHelper
     *
     * @var string
     */
    protected $catalogHelper;

    /**
     * CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollection;

    /**
     * CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory
     */
    public $warehouseCollection;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Template
     *
     * @var string
     */
    protected $_template = "order/view/tab/assign.phtml";

    /**
     * Constructor method
     *
     * @param \Magento\Framework\Registry                                 $registry            registry
     * @param \Magento\Framework\Data\Form\FormKey                        $formKey             formKey
     * @param \Magento\Backend\Block\Template\Context                     $context             context
     * @param \Magento\Framework\App\ResourceConnection                   $resource            resource
     * @param \Magento\Catalog\Model\Product\OptionFactory                $optionFactory       optionFactory
     * @param \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory     $orderCollection     orderCollection
     * @param \Webkul\WMS\Model\ResourceModel\Warehouse\CollectionFactory $warehouseCollection warehouseCollection
     * @param array                                                       $data                data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Webkul\WMS\Model\ResourceModel\Warehouse\CollectionFactory $warehouseCollection,
        array $data = []
    ) {
        $this->formKey = $formKey;
        $this->resource = $resource;
        $this->coreRegistry = $registry;
        $this->catalogHelper = $catalogHelper;
        $this->optionFactory = $optionFactory;
        $this->orderCollection = $orderCollection;
        $this->warehouseCollection = $warehouseCollection;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve helper instance
     *
     * @return \Magento\Catalog\Helper\Data
     */
    public function getCatalogHelper()
    {
        return $this->catalogHelper;
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry("current_order");
    }

    /**
     * Get order options
     *
     * @param \Magento\Sales\Model\Order\Item $item item
     *
     * @return array
     */
    public function getOrderOptions($item)
    {
        $result = [];
        if ($options = $item->getProductOptions()) {
            if (isset($options["options"])) {
                $result = array_merge($result, $options["options"]);
            }
            if (isset($options["additional_options"])) {
                $result = array_merge($result, $options["additional_options"]);
            }
            if (!empty($options["attributes_info"])) {
                $result = array_merge($options["attributes_info"], $result);
            }
        }
        return $result;
    }

    /**
     * Return custom option html
     *
     * @param array $optionInfo optionInfo
     *
     * @return string
     */
    public function getCustomizedOptionValue($optionInfo)
    {
        $_default = $optionInfo["value"];
        if (isset($optionInfo["option_type"])) {
            try {
                $group = $this->optionFactory
                    ->create()
                    ->groupFactory($optionInfo["option_type"]);
                return $group->getCustomizedView($optionInfo);
            } catch (\Exception $e) {
                return $_default;
            }
        }
        return $_default;
    }

    /**
     * Add line breaks and truncate value
     *
     * @param string $value value
     *
     * @return array
     */
    public function getFormattedOption($value)
    {
        $remainder = "";
        $this->truncateString($value, 55, "", $remainder);
        $result = [
            "value" => nl2br($this->truncateResult->getValue()),
            "remainder" => nl2br($this->truncateResult->getRemainder())
        ];
        return $result;
    }

    /**
     * Truncate string
     *
     * @param string $value      value
     * @param int    $length     length
     * @param string $etc        etc
     * @param string $remainder  remainder
     * @param bool   $breakWords breakWords
     *
     * @return string
     */
    public function truncateString($value, $length = 80, $etc = "...", &$remainder = "", $breakWords = true)
    {
        $this->truncateResult = $this->filterManager
            ->truncateFilter(
                $value,
                [
                    "etc" => $etc,
                    "length" => $length,
                    "breakWords" => $breakWords
                ]
            );
        return $this->truncateResult->getValue();
    }

    /**
     * Get Order items
     *
     * @return array
     */
    public function getOrderItems()
    {
        return $this->getOrder()->getAllVisibleItems();
    }

    /**
     * Get Warehouse list
     *
     * @param string $sku sku
     *
     * @return warehouseCollection
     */
    public function getWarehouseList($sku)
    {
        $collection = $this->warehouseCollection->create();
        $IT = $this->resource->getTableName("inventory_source_item");
        $where = "main_table.source=IT.source_code AND IT.sku='$sku'";
        $collection->getSelect()->join(
            [
                "IT" => $IT
            ],
            $where,
            []
        );
        $collection->addFieldToFilter("main_table.status", 1);
        return $collection;
    }

    /**
     * Check is warehouse selected
     *
     * @param integer $productId   productId
     * @param integer $warehouseId warehouseId
     * @param integer $orderId     orderId
     *
     * @return boolean
     */
    public function isWarehouseSelected($productId, $warehouseId, $orderId)
    {
        $wareHouse = $this->orderCollection->create()
            // ->addFieldToSelect("id", "order_id", "warehouse_id", "product_id")
            ->addFieldToFilter("order_id", $orderId)
            ->addFieldToFilter("product_id", $productId)
            ->addFieldToFilter("warehouse_id", $warehouseId)
            ->getFirstItem();
        if ($wareHouse->getId()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Assigned order details
     *
     * @return array
     */
    public function getAssignmentDetails()
    {
        $assignedOrder = $this->orderCollection->create()
            ->addFieldToFilter("order_id", $this->getOrder()->getId())
            ->getFirstItem();
        return $assignedOrder;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __("Assign Order");
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __("Assign Order");
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    public function canAssignWarehouse()
    {
        $order = $this->getOrder();
        $state = $order->getState();
        return false === in_array($state, ['canceled', 'closed', 'complete']);
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Get StaffList Url
     *
     * @return string
     */
    public function getStaffListUrl()
    {
        return $this->getUrl(
            "wms/order/getstafflist",
            [
                "_current" => true
            ]
        );
    }

    /**
     * Get StaffList Url
     *
     * @return string
     */
    public function getFormUrl()
    {
        return $this->getUrl(
            "wms/order/assign",
            [
                "_current" => true
            ]
        );
    }

    /**
     * Get form key
     *
     * @return string
     */
    public function getFormKey()
    {
         return $this->formKey->getFormKey();
    }
}

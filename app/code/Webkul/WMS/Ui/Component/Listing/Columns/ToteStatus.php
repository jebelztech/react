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

namespace Webkul\WMS\Ui\Component\Listing\Columns;

/**
 * ToteStatus Class warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class ToteStatus extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * OrderTotes CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\OrderTotes\CollectionFactory
     */
    protected $orderTotes;

    /**
     * OrderStatus CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\OrderStatus\CollectionFactory
     */
    protected $orderStatus;

    /**
     * Constructor function
     *
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface  $context            context
     * @param \Magento\Framework\View\Element\UiComponentFactory            $uiComponentFactory uiComponentFactory
     * @param \Webkul\WMS\Model\ResourceModel\OrderTotes\CollectionFactory  $orderTotes         orderTotes
     * @param \Webkul\WMS\Model\ResourceModel\OrderStatus\CollectionFactory $orderStatus        orderStatus
     * @param array                                                         $components         components
     * @param array                                                         $data               data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Webkul\WMS\Model\ResourceModel\OrderTotes\CollectionFactory $orderTotes,
        \Webkul\WMS\Model\ResourceModel\OrderStatus\CollectionFactory $orderStatus,
        array $components = [],
        array $data = []
    ) {
        $this->orderTotes = $orderTotes;
        $this->orderStatus = $orderStatus;
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
    }

    /**
     * Preparedatasource function
     *
     * @param array $dataSource dataSource
     *
     * @return void
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            $fieldName = $this->getData("name");
            foreach ($dataSource["data"]["items"] as &$item) {
                $orderTotes = $this->orderTotes
                    ->create()
                    ->addFieldToSelect(
                        [
                            "order_id",
                            "assigned_tote_id"
                        ]
                    )
                    ->addFieldToFilter("assigned_tote_id", $item["id"])
                    ->getFirstItem();
                $toteStatus = $this->orderStatus
                    ->create()
                    ->addFieldToSelect(
                        [
                            "status",
                            "order_id"
                        ]
                    )
                    ->addFieldToFilter("order_id", $orderTotes->getOrderId())
                    ->getFirstItem();
                $isToteVacant = "Not Assigned";
                if ($toteStatus->getStatus() && in_array($toteStatus->getStatus(), ["started", "picked"])) {
                    $isToteVacant = "Assigned";
                }
                $item[$this->getData("name")] = $isToteVacant;
            }
        }
        return $dataSource;
    }
}

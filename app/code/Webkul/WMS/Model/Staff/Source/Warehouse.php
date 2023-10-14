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

namespace Webkul\WMS\Model\Staff\Source;

use \Webkul\WMS\Model\ResourceModel\Warehouse\CollectionFactory;

/**
 * Class Warehouse staff
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Warehouse implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Warehouse
     *
     * @var CollectionFactory
     */
    protected $collection;

    /**
     * Constructor
     *
     * @param CollectionFactory $collection collection
     */
    public function __construct(
        CollectionFactory $collection
    ) {
        $this->collection = $collection;
    }

    /**
     * Get warehouse list
     *
     * @return array
     */
    public function toOptionArray()
    {
        $warehouseList[] = [
            "label" => __("Please Select"),
            "value" => ""
        ];
        foreach ($this->collection->create() as $each) {
            $warehouseList[] = [
                "label" => $each->getTitle(),
                "value" => $each->getId()
            ];
        }
        return $warehouseList;
    }
}

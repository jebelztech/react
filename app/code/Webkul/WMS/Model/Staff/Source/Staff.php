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

use \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory;

/**
 * Class Staff warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Staff implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Staff
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
                "label" => $each->getName(),
                "value" => $each->getId()
            ];
        }
        return $warehouseList;
    }
}

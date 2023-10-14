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

namespace Webkul\WMS\Model\Warehouse\Source;

/**
 * Class Status warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Warehouse
     *
     * @var \Webkul\WMS\Model\Warehouse
     */
    protected $warehouse;

    /**
     * Constructor
     *
     * @param \Webkul\WMS\Model\Warehouse $warehouse warehouse
     */
    public function __construct(
        \Webkul\WMS\Model\Warehouse $warehouse
    ) {
        $this->warehouse = $warehouse;
    }

    /**
     * Get status options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->warehouse->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                "label" => $value,
                "value" => $key
            ];
        }
        return $options;
    }
}

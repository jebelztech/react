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

namespace Webkul\WMS\Model\Order\Source;

/**
 * Class Status order
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Get status list
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                "label" =>__("Initiated"),
                "value" => "initiated"
            ],
            [
                "label" => __("Started"),
                "value" => "started"
            ],
            [
                "label" => __("Picked"),
                "value" => "picked"
            ],
            [
                "label" => __("Packed"),
                "value" => "packed"
            ]
        ];
    }
}

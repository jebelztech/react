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

/**
 * Class Gender staff
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Gender implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Get gender options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                "label" => __("Please Select"),
                "value" => ""
            ],
            [
                "label" => __("Male"),
                "value" => "male"
            ],
            [
                "label" => __("Female"),
                "value" => "female"
            ],
            [
                "label" => __("Transgender"),
                "value" => "tensgender"
            ]
        ];
    }
}

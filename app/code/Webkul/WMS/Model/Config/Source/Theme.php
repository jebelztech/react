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

namespace Webkul\WMS\Model\Config\Source;

/**
 * Class Theme config
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Theme implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * Function toOptionArray
     *
     * @return Array
     */
    public function toOptionArray()
    {
        return  [
            [
                "value"=>"dark",
                "label"=>__("Dark Theme")
            ],
            [
                "value"=>"light",
                "label"=>__("Light Theme")
            ]
        ];
    }
}

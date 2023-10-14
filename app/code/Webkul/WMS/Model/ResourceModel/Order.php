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

namespace Webkul\WMS\Model\ResourceModel;

/**
 * Class Order warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Order extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store id
     *
     * @var integer
     */
    protected $store = null;

    /**
     * Contructor of class Order
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init("wms_order", "id");
    }

    /**
     * Load Order
     *
     * @param AbstractModel $object object
     * @param mixed         $value  value
     * @param string        $field  field
     *
     * @return Order
     */
    public function load(
        \Magento\Framework\Model\AbstractModel $object,
        $value,
        $field = null
    ) {
        if (!is_numeric($value) && $field === null) {
            $field = "id";
        }
        return parent::load($object, $value, $field);
    }
}

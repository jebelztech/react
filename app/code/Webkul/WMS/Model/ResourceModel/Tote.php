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
 * Class Tote warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Tote extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $store = null;

    /**
     * Contructor of class Tote
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init("wms_tote", "id");
    }

    /**
     * Load Tote
     *
     * @param AbstractModel $object object
     * @param mixed         $value  value
     * @param string        $field  field
     *
     * @return Tote
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

    /**
     * Set Tote storeid
     *
     * @param integer $store store
     *
     * @return Tote
     */
    public function setStore($store)
    {
        $this->store = $store;
        return $this;
    }

    /**
     * Get Tote store
     *
     * @return mixed
     */
    public function getStore()
    {
        return $this->_storeManager->getStore($this->store);
    }
}

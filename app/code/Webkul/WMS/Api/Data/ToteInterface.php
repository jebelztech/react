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

namespace Webkul\WMS\Api\Data;

/**
 * Class ToteInterface api
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
interface ToteInterface
{
    const ID = "id";
    const TITLE = "title";
    const WAREHOUSE_ID = "warehouse_id";

    /**
     * Function getId
     *
     * @return integer
     */
    public function getId();

    /**
     * Function setId
     *
     * @param integer $id id
     *
     * @return null
     */
    public function setId($id);

    /**
     * Function getTitle
     *
     * @return string
     */
    public function getTitle();

    /**
     * Function setTitle
     *
     * @param string $title title
     *
     * @return null
     */
    public function setTitle($title);

    /**
     * Function getWarehouseId
     *
     * @return string
     */
    public function getWarehouseId();

    /**
     * Function setWarehouseId
     *
     * @param string $warehouse_id warehouse_id
     *
     * @return null
     */
    public function setWarehouseId($warehouse_id);
}

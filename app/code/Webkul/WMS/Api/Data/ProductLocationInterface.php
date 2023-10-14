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
 * Class ProductLocationInterface api
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
interface ProductLocationInterface
{
    const ID = "id";
    const ROW = "row";
    const RACK = "rack";
    const SHELF = "shelf";
    const COLUMN = "column";
    const PRODUCT_ID = "product_id";
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
     * Function getRow
     *
     * @return integer
     */
    public function getRow();

    /**
     * Function setRow
     *
     * @param integer $row row
     *
     * @return null
     */
    public function setRow($row);

    /**
     * Function getRack
     *
     * @return integer
     */
    public function getRack();

    /**
     * Function setRack
     *
     * @param integer $rack rack
     *
     * @return null
     */
    public function setRack($rack);

    /**
     * Function getShelf
     *
     * @return string
     */
    public function getShelf();

    /**
     * Function setShelf
     *
     * @param string $shelf shelf
     *
     * @return null
     */
    public function setShelf($shelf);

    /**
     * Function getColumn
     *
     * @return integer
     */
    public function getColumn();

    /**
     * Function setColumn
     *
     * @param integer $column column
     *
     * @return null
     */
    public function setColumn($column);

    /**
     * Function getIncrementId
     *
     * @return integer
     */
    public function getProductId();

    /**
     * Function setProductId
     *
     * @param integer $product_id product_id
     *
     * @return null
     */
    public function setProductId($product_id);

    /**
     * Function getWarehouseId
     *
     * @return integer
     */
    public function getWarehouseId();

    /**
     * Function setWarehouseId
     *
     * @param integer $warehouse_id warehouse_id
     *
     * @return null
     */
    public function setWarehouseId($warehouse_id);
}

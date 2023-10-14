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
 * Class WarehouseInterface api
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
interface WarehouseInterface
{
    const ID = "id";
    const TITLE = "title";
    const STATUS = "status";
    const SOURCE = "source";
    const ROW_COUNT = "row_count";
    const TOTE_COUNT = "tote_count";
    const CREATED_AT = "created_at";
    const UPDATED_AT = "updated_at";
    const COLUMN_COUNT = "column_count";
    const RACKS_PER_SHELF = "racks_per_shelf";
    const SHELVES_PER_CLUSTER = "shelves_per_cluster";

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
     * Function getStatus
     *
     * @return integer
     */
    public function getStatus();

    /**
     * Function setStatus
     *
     * @param integer $status status
     *
     * @return null
     */
    public function setStatus($status);

    /**
     * Function getSource
     *
     * @return string
     */
    public function getSource();

    /**
     * Function setSource
     *
     * @param string $source source
     *
     * @return null
     */
    public function setSource($source);

    /**
     * Function getRowCount
     *
     * @return integer
     */
    public function getRowCount();

    /**
     * Function setRowCount
     *
     * @param integer $row_count row_count
     *
     * @return null
     */
    public function setRowCount($row_count);

    /**
     * Function getToteCount
     *
     * @return string
     */
    public function getToteCount();

    /**
     * Function setToteCount
     *
     * @param string $tote_count tote_count
     *
     * @return null
     */
    public function setToteCount($tote_count);

    /**
     * Function getCreatedAt
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Function setCreatedAt
     *
     * @param string $created_at created_at
     *
     * @return null
     */
    public function setCreatedAt($created_at);

    /**
     * Function getUpdatedAt
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Function setUpdatedAt
     *
     * @param string $updated_at updated_at
     *
     * @return null
     */
    public function setUpdatedAt($updated_at);

    /**
     * Function getColumnCount
     *
     * @return integer
     */
    public function getColumnCount();

    /**
     * Function setColumnCount
     *
     * @param integer $column_count column_count
     *
     * @return null
     */
    public function setColumnCount($column_count);

    /**
     * Function getRacksPerShelf
     *
     * @return integer
     */
    public function getRacksPerShelf();

    /**
     * Function setRacksPerShelf
     *
     * @param integer $racks_per_shelf racks_per_shelf
     *
     * @return null
     */
    public function setRacksPerShelf($racks_per_shelf);

    /**
     * Function getShelvesPerCluster
     *
     * @return integer
     */
    public function getShelvesPerCluster();

    /**
     * Function setShelvesPerCluster
     *
     * @param integer $shelves_per_cluster shelves_per_cluster
     *
     * @return null
     */
    public function setShelvesPerCluster($shelves_per_cluster);
}

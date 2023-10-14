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

namespace Webkul\WMS\Api;

use Webkul\WMS\Api\Data\StaffInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class StaffRepositoryInterface api
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
interface StaffRepositoryInterface
{
    /**
     * Function getById
     *
     * @param integer $staffId staffId
     *
     * @return mixed
     */
    public function getById($staffId);

    /**
     * Function deleteById
     *
     * @param integer $staffId staffId
     *
     * @return null
     */
    public function deleteById($staffId);

    /**
     * Function save
     *
     * @param StaffInterface $staff staff
     *
     * @return null
     */
    public function save(StaffInterface $staff);

    /**
     * Function delete
     *
     * @param StaffInterface $staff staff
     *
     * @return null
     */
    public function delete(StaffInterface $staff);

    /**
     * Function getList
     *
     * @param SearchCriteriaInterface $searchCriteria searchCriteria
     *
     * @return null
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}

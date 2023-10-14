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

use Webkul\WMS\Api\Data\ToteInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class ToteRepositoryInterface api
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
interface ToteRepositoryInterface
{
    /**
     * Function getById
     *
     * @param integer $toteId toteId
     *
     * @return mixed
     */
    public function getById($toteId);

    /**
     * Function deleteById
     *
     * @param integer $toteId toteId
     *
     * @return null
     */
    public function deleteById($toteId);

    /**
     * Function save
     *
     * @param ToteInterface $tote tote
     *
     * @return null
     */
    public function save(ToteInterface $tote);

    /**
     * Function delete
     *
     * @param ToteInterface $tote tote
     *
     * @return null
     */
    public function delete(ToteInterface $tote);

    /**
     * Function getList
     *
     * @param SearchCriteriaInterface $searchCriteria searchCriteria
     *
     * @return null
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}

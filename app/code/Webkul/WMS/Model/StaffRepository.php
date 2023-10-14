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

namespace Webkul\WMS\Model;

use Webkul\WMS\Api\Data\StaffInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class StaffRepository warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class StaffRepository implements \Webkul\WMS\Api\StaffRepositoryInterface
{
    /**
     * StaffFactory
     *
     * @var StaffFactory
     */
    protected $staffFactory;

    /**
     * Staff
     *
     * @var ResourceModel\Staff
     */
    protected $resourceModel;

    /**
     * CollectionFactory
     *
     * @var ResourceModel\Staff\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Array
     *
     * @var array
     */
    protected $instancesById = [];

    /**
     * Contructor of class StaffRepository
     *
     * @param StaffFactory                          $staffFactory      Staff
     * @param ResourceModel\Staff                   $resourceModel     Staffmodel
     * @param ResourceModel\Staff\CollectionFactory $collectionFactory StaffCollection
     */
    public function __construct(
        StaffFactory $staffFactory,
        ResourceModel\Staff $resourceModel,
        ResourceModel\Staff\CollectionFactory $collectionFactory
    ) {
        $this->staffFactory = $staffFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Save Staff
     *
     * @param StaffInterface $staff Staff
     *
     * @return mixed
     */
    public function save(StaffInterface $staff)
    {
        try {
            $this->resourceModel->save($staff);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                $e->getMessage()
            );
        }
        unset($this->instancesById[$staff->getId()]);
        return $this->getById($staff->getId());
    }

    /**
     * Get Staff
     *
     * @param integer $staffId staffId
     *
     * @return StaffInterface
     */
    public function getById($staffId)
    {
        $staffData = $this->staffFactory->create();
        $staffData->load($staffId);
        $this->instancesById[$staffId] = $staffData;
        return $this->instancesById[$staffId];
    }

    /**
     * Get list of wahrehouse
     *
     * @param SearchCriteriaInterface $searchCriteria searchCriteria
     *
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $collection->load();
        return $collection;
    }

    /**
     * Delete Staff
     *
     * @param StaffInterface $staff staff
     *
     * @return boolean
     */
    public function delete(StaffInterface $staff)
    {
        $staffId = $staff->getId();
        try {
            $this->resourceModel->delete($staff);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __("Unable to remove staff with id %1", $staffId)
            );
        }
        unset($this->instancesById[$staffId]);
        return true;
    }

    /**
     * Delete staff by id
     *
     * @param integer $staffId staffId
     *
     * @return boolean
     */
    public function deleteById($staffId)
    {
        $staff = $this->getById($staffId);
        return $this->delete($staff);
    }
}

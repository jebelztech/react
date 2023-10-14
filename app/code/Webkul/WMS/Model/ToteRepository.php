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

use Webkul\WMS\Api\Data\ToteInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class ToteRepository warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class ToteRepository implements \Webkul\WMS\Api\ToteRepositoryInterface
{
    protected $toteFactory;
    protected $resourceModel;
    protected $collectionFactory;
    protected $instancesById = [];
    protected $extensibleDataObjectConverter;

    /**
     * Contructor of class ToteRepository
     *
     * @param ToteFactory                          $toteFactory      Tote
     * @param ResourceModel\Tote                   $resourceModel     Totemodel
     * @param ResourceModel\Tote\CollectionFactory $collectionFactory ToteCollection
     */
    public function __construct(
        ToteFactory $toteFactory,
        ResourceModel\Tote $resourceModel,
        ResourceModel\Tote\CollectionFactory $collectionFactory
    ) {
        $this->toteFactory = $toteFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Save Tote
     *
     * @param ToteInterface $tote Tote
     *
     * @return mixed
     */
    public function save(ToteInterface $tote)
    {
        try {
            $this->resourceModel->save($tote);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                $e->getMessage()
            );
        }
        unset($this->instancesById[$tote->getId()]);
        return $this->getById($tote->getId());
    }

    /**
     * Get Tote
     *
     * @param integer $toteId toteId
     *
     * @return ToteInterface
     */
    public function getById($toteId)
    {
        $toteData = $this->toteFactory->create();
        $toteData->load($toteId);
        $this->instancesById[$toteId] = $toteData;
        return $this->instancesById[$toteId];
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
     * Delete Tote
     *
     * @param ToteInterface $tote tote
     *
     * @return boolean
     */
    public function delete(ToteInterface $tote)
    {
        $toteId = $tote->getId();
        try {
            $this->resourceModel->delete($tote);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __("Unable to remove tote with id %1", $toteId)
            );
        }
        unset($this->instancesById[$toteId]);
        return true;
    }

    /**
     * Delete tote by id
     *
     * @param integer $toteId toteId
     *
     * @return boolean
     */
    public function deleteById($toteId)
    {
        $tote = $this->getById($toteId);
        return $this->delete($tote);
    }
}

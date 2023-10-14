<?php
/**
 * Webkul Software.
 *
 *
 * @category  Webkul
 * @package   Webkul_DeliveryBoy
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */
namespace Webkul\DeliveryBoy\Model;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Webkul\DeliveryBoy\Model\ResourceModel\OrderTransaction\CollectionFactory as OrderTransactionCollF;
use Webkul\DeliveryBoy\Api\Data\OrderTransactionInterface;
use Webkul\DeliveryBoy\Api\Data\OrderTransactionSearchResultInterfaceFactory as SearchResultFactory;

class OrderTransactionRepository implements \Webkul\DeliveryBoy\Api\OrderTransactionRepositoryInterface
{
    /**
     * @var SearchResultFactory
     */
    protected $searchResultFactory = null;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var OrderInterface[]
     */
    protected $registry = [];

    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var OrderTransactionCollF
     */
    private $orderTransactionCollF;

    /**
     * @param SearchResultFactory $searchResultFactory
     * @param CollectionProcessorInterface|null $collectionProcessor
     * @param JsonSerializer|null $serializer
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(
        SearchResultFactory $searchResultFactory,
        CollectionProcessorInterface $collectionProcessor = null,
        JsonSerializer $serializer = null,
        JoinProcessorInterface $extensionAttributesJoinProcessor = null,
        OrderTransactionCollF $orderTransactionCollF
    ) {
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->serializer = $serializer;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->orderTransactionCollF = $orderTransactionCollF;
    }

    /**
     * @param int $id
     * @return \Magento\Sales\Api\Data\OrderTransactionInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        if (!$id) {
            throw new InputException(__('An ID is needed. Set the ID and try again.'));
        }
        if (!isset($this->registry[$id])) {
            /** @var OrderTransactionInterface $entity */
            $entity = $this->orderTransactionCollF->create()
            ->addFieldToFilter(
                OrderTransactionInterface::ID,
                $id
            )->getFirstItem();
            if (!$entity->getEntityId()) {
                throw new NoSuchEntityException(
                    __("The entity that was requested doesn't exist. Verify the entity and try again.")
                );
            }
            $this->registry[$id] = $entity;
        }
        return $this->registry[$id];
    }

    /**
     * Find entities by criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magento\Sales\Api\Data\OrderSearchResultInterface $searchResult */
        $searchResult = $this->searchResultFactory->create();
        $this->collectionProcessor->process($searchCriteria, $searchResult);
        $searchResult->setSearchCriteria($searchCriteria);
        return $searchResult;
    }

    /**
     * @param OrderTransactionInterface $entity
     * @return bool
     */
    public function delete(OrderTransactionInterface $entity)
    {
        $entity->delete();
        unset($this->registry[$entity->getEntityId()]);
        return true;
    }

    /**
     * Delete entity by Id
     *
     * @param int $id
     * @return bool
     */
    public function deleteById($id)
    {
        $entity = $this->get($id);
        return $this->delete($entity);
    }

    /**
     * @param OrderTransactionInterface $entity
     * @return OrderTransactionInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(OrderTransactionInterface $entity)
    {
        $entity->save();
        $this->registry[$entity->getEntityId()] = $entity;
        return $this->registry[$entity->getEntityId()];
    }
}

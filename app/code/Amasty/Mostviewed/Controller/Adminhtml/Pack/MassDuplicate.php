<?php

namespace Amasty\Mostviewed\Controller\Adminhtml\Pack;

use Amasty\Mostviewed\Api\Data\PackInterface;

class MassDuplicate extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    protected function itemAction(PackInterface $pack)
    {
        $pack = $this->repository->getById($pack->getPackId(), true);
        $this->repository->duplicate($pack);
    }

    /**
     * {@inheritdoc}
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        return __('A total of %1 record(s) have been duplicated.', $collectionSize);
    }
}

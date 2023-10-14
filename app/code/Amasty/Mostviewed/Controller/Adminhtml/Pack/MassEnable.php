<?php

namespace Amasty\Mostviewed\Controller\Adminhtml\Pack;

use Amasty\Mostviewed\Api\Data\PackInterface;

/**
 * Class MassDelete
 */
class MassEnable extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    protected function itemAction(PackInterface $pack)
    {
        $pack->setStatus(1);
        $this->repository->save($pack);
    }
}

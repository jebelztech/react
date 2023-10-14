<?php

namespace Amasty\Mostviewed\Controller\Adminhtml\Product\Group;

use Amasty\Mostviewed\Api\Data\GroupInterface;

/**
 * Class MassDelete
 */
class MassEnable extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    protected function itemAction(GroupInterface $group)
    {
        $group->setStatus(1);
        $this->repository->save($group);
    }
}

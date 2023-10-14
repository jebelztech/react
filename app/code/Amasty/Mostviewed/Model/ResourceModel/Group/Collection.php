<?php

namespace Amasty\Mostviewed\Model\ResourceModel\Group;

use Amasty\Mostviewed\Model\ResourceModel\Group;
use Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'group_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\Mostviewed\Model\Group::class, Group::class);
    }
}

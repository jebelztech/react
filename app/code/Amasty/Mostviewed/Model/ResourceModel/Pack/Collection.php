<?php

namespace Amasty\Mostviewed\Model\ResourceModel\Pack;

use Amasty\Mostviewed\Model\ResourceModel\Pack;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'pack_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\Mostviewed\Model\Pack::class, Pack::class);
    }
}

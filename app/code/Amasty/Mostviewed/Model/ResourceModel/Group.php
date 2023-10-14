<?php

namespace Amasty\Mostviewed\Model\ResourceModel;

use Magento\Rule\Model\ResourceModel\AbstractResource;

class Group extends AbstractResource
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amasty_mostviewed_group', 'group_id');
    }
}

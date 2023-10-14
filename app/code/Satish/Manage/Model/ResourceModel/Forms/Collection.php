<?php

namespace Satish\Manage\Model\ResourceModel\Forms;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Satish\Manage\Model\Forms', 'Satish\Manage\Model\ResourceModel\Forms');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>
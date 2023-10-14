<?php

namespace Etail\Career\Model\ResourceModel\Careers;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Etail\Career\Model\Careers', 'Etail\Career\Model\ResourceModel\Careers');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>
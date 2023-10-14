<?php
namespace Etail\Career\Model\ResourceModel;

class Careers extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('etail_careers', 'career_id');
    }
}
?>
<?php
namespace Satish\Manage\Model\ResourceModel;

class Imagess extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('manage_images', 'id');
    }
}
?>
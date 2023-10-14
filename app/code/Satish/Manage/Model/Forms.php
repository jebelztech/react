<?php
namespace Satish\Manage\Model;

class Forms extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Satish\Manage\Model\ResourceModel\Forms');
    }
}
?>
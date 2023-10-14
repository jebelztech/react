<?php
namespace Etail\Career\Model;

class Careers extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Etail\Career\Model\ResourceModel\Careers');
    }
}
?>
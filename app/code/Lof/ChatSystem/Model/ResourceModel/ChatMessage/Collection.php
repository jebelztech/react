<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ChatSystem
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\ChatSystem\Model\ResourceModel\ChatMessage;

use \Lof\ChatSystem\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'message_id';
    /**
     * Define resource model
     *
     * @return void
     */

     /**
      * Perform operations after collection load
      *
      * @return $this
      */
    protected function _afterLoad()
    {
        //$this->performAfterLoad('lof_chatsystem_category_store', 'category_id');
        // $this->getProductsAfterLoad();
        return parent::_afterLoad();
    }


    protected function _construct()
    {
        $this->_init('Lof\ChatSystem\Model\ChatMessage', 'Lof\ChatSystem\Model\ResourceModel\ChatMessage');
    }

      /**
       * Returns pairs category_id - title
       *
       * @return array
       */
    public function toOptionArray()
    {
        return $this->_toOptionArray('message_id', 'title');
    }
    /**
     * Add link attribute to filter.
     *
     * @param string $code
     * @param array $condition
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $this->performAddStoreFilter($store, $withAdmin);

        return $this;
    }
}

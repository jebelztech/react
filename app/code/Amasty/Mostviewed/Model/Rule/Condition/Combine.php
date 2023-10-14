<?php

namespace Amasty\Mostviewed\Model\Rule\Condition;

class Combine extends \Magento\CatalogRule\Model\Rule\Condition\Combine
{
    /**
     * @return string
     */
    public function getPrefix()
    {
        return 'where_conditions';
    }

    /**
     * @return mixed
     */
    public function getWhereConditions()
    {
        return $this->getData('conditions');
    }
}

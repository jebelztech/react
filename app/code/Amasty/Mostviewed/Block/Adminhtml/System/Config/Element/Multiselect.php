<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Block\Adminhtml\System\Config\Element;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement as AbstractElement;

class Multiselect extends Field
{
    const DEFAULT = 10;

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $count = count($element->getValues()) ?: self::DEFAULT;
        $element->setData('size', ($count <= self::DEFAULT) ? $count : self::DEFAULT);

        return $element->getElementHtml();
    }
}

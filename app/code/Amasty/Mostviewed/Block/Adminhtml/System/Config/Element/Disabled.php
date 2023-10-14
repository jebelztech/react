<?php

namespace Amasty\Mostviewed\Block\Adminhtml\System\Config\Element;

class Disabled extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @inheritdoc
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setDisabled(true);
        return parent::render($element);
    }
}

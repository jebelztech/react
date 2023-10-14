<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Block\Widget;

use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class PackWidgetWrapper extends Template
{
    /**
     * @var ModuleManager
     */
    private $moduleManager;

    public function __construct(
        ModuleManager $moduleManager,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return string
     */
    public function _toHtml(): string
    {
        $html = '';
        if ($this->moduleManager->isEnabled('Amasty_Mostviewed')) {
            $originalBlock = $this->getLayout()->createBlock(
                Pack::class,
                '',
                ['data' => $this->getData()]
            );
            $originalBlock->setTemplate($this->getTemplate());

            $html = $originalBlock->toHtml();
        }

        return $html;
    }
}

<?php

namespace Amasty\Mostviewed\Block\Product;

class BundlePackTab extends BundlePack
{
    /**
     * @return string
     */
    public function toHtml()
    {
        $html = trim(parent::toHtml());
        if ($html) {
            $this->setTitle($this->config->getTabTitle());
        }

        return $html;
    }
}

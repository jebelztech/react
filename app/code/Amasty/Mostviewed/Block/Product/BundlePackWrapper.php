<?php

namespace Amasty\Mostviewed\Block\Product;

class BundlePackWrapper extends BundlePack
{
    /**
     * @return string
     */
    public function toHtml()
    {
        return $this->getParentHtml();
    }
}

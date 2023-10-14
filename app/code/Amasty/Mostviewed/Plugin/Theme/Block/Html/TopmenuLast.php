<?php

namespace Amasty\Mostviewed\Plugin\Theme\Block\Html;

use Amasty\Mostviewed\Model\OptionSource\TopMenuLink;

class TopmenuLast extends Topmenu
{
    /**
     * @return int
     */
    protected function getPosition()
    {
        return TopMenuLink::DISPLAY_LAST;
    }
}

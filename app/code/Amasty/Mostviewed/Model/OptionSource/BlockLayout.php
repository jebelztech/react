<?php

namespace Amasty\Mostviewed\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class BlockLayout implements OptionSourceInterface
{
    const SLIDER = 1;

    const GRID = 0;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::GRID, 'label' => __('Grid')],
            ['value' => self::SLIDER, 'label' => __('Slider')]
        ];
    }
}

<?php
namespace Satish\Manage\Plugin\Catalog\Model;
class Config
{
    public function afterGetAttributeUsedForSortByArray(
    \Magento\Catalog\Model\Config $catalogConfig,
    $options
    ) {

        $options['low_to_high'] = __('Low Price');
        $options['high_to_low'] = __('High price');
        //$options['top_rating'] = __('Top Rating');
        return $options;

    }

}
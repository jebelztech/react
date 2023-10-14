<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\ResourceModel\Pack\Analytic\PackSales;

class Table
{
    const TABLE_NAME = 'amasty_mostviewed_pack_sales';

    const ID_COLUMN = 'id';
    const PACK_ID_COLUMN = 'pack_id';
    const PACK_NAME_COLUMN = 'pack_name';
    const PRODUCT_NAMES_COLUMN = 'product_names';
    const ORDER_ID_COLUMN = 'order_id';
    const BASE_SUBTOTAL_ID_COLUMN = 'base_subtotal';
    const BASE_TOTAL_ID_COLUMN = 'base_grand_total';
    const TOTAL_ID_COLUMN = 'grand_total';
}

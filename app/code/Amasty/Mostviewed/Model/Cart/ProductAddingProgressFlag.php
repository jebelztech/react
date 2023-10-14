<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Cart;

class ProductAddingProgressFlag
{
    /**
     * @var boolean
     */
    private $flag = false;

    public function get(): bool
    {
        return $this->flag;
    }

    public function set(bool $flag): void
    {
        $this->flag = $flag;
    }
}

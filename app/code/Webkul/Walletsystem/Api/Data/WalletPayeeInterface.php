<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_Walletsystem
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\Walletsystem\Api\Data;

/**
 * Webkul Walletsystem Wallet Payee Interface
 */
interface WalletPayeeInterface
{
    const ENTITY_ID = 'entity_id';

    /**
     * Get entity ID
     *
     * @return int|null
     */
    public function getEntityId();

    /**
     * Set Entity ID
     *
     * @param int $id
     * @return \Webkul\Walletsystem\Api\Data\WalletPayeeInterface
     */
    public function setEntityId($id);
}

<?php
/**
* Webkul Software.
*
* @category  Webkul
* @package   Webkul_Mobikulwallet
* @author    Webkul
* @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
* @license   https://store.webkul.com/license.html
*/
namespace Webkul\Mobikulwallet\Api;
 
interface AccountdetailsInterface
{
    /**
     * Returns account details
     *
     * @api
     * @param string $customerToken Users id.
     * @return \Webkul\Mobikulwallet\Api\ResponseInterface.
     */
    public function getAccountDetails(
        $customerToken = null
    );

    /**
     * Returns message and status
     *
     * @api
     * @param string $customerToken Users id.
     * @param int $id acount details id
     * @return \Webkul\Mobikulwallet\Api\ResponseInterface.
     */
    public function deleteRequest(
        $customerToken = NULL,
        $id = 0
    );

    /**
     * Returns message and status
     *
     * @api
     * @param string $customerToken Users id.
     * @param string $holdername
     * @param string $bankname
     * @param string $bankcode
     * @param string $additional
     * @return \Webkul\Mobikulwallet\Api\ResponseInterface.
     */
    public function saveAccountDetails(
        $customerToken = NULL,
        $holdername = "",
        $bankname = "",
        $bankcode = "",
        $additional = ""
    );
}
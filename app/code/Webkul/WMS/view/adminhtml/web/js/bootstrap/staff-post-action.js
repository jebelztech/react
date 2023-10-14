
/**
 * Webkul Software.
 * 
 * PHP version 7.0+
 *
 * @category  Webkul
 * @package   Webkul_WMS
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

require(
    [
        "jquery",
        "Magento_Ui/js/modal/confirm",
        "mage/translate"
    ], function ($, confirm) {
        "use strict";

        function getStaffForm(url) {
            return $("<form>", {"action":url, "method":"POST"})
            .append($("<input>", {
                    "name": "form_key",
                    "value": window.FORM_KEY,
                    "type": "hidden"
                })
            );
        }

        $("#staff-edit-delete-button").click(function () {
            var confirmationMsg = $.mage.__("Are you sure you want to do this?");
            var deleteUrl = $("#staff-edit-delete-button").data("url");
            confirm({
                "content": confirmationMsg,
                "actions": {
                    confirm: function () {
                        getStaffForm(deleteUrl).appendTo("body").submit();
                    }
                }
            });
            return false;
        });
    }
);
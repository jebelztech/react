
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
    ],
    function ($, confirm) {
        "use strict";
        function getWarehouseForm(url){
            return $("<form>", {"action":url, "method":"POST"})
            .append($("<input>", {
                    "name": "form_key",
                    "value": window.FORM_KEY,
                    "type": "hidden"
                })
            );
        }

        $("#warehouse-edit-delete-button").click(function() {
            var confirmationMsg = $.mage.__("Are you sure you want to do this?");
            var deleteUrl = $("#warehouse-edit-delete-button").data("url");
            confirm({
                "content": confirmationMsg,
                "actions": {
                    confirm: function () {
                        getWarehouseForm(deleteUrl).appendTo("body").submit();
                    }
                }
            });
            return false;
        });

        $(document).ajaxSuccess(function (event, xhr, settings) {
            if (typeof $("#warehouse-edit-delete-button").data("url") != "undefined") {
                var target = [];
                target.push($("input[name='wms_warehouse\\[tote_count\\]']"));
                target.push($("input[name='wms_warehouse\\[row_count\\]']"));
                target.push($("input[name='wms_warehouse\\[column_count\\]']"));
                target.push($("input[name='wms_warehouse\\[racks_per_shelf\\]']"));
                target.push($("input[name='wms_warehouse\\[shelves_per_cluster\\]']"));

                for (var i=0; i<target.length; i++) {
                    target[i].attr("disabled", "disabled");
                    if (target[i].next().hasClass("changer")) {
                        target[i].next().remove();
                    }
                    var className = "changer";
                    if (i == 1) {
                        className += " rowCount";
                    } else if (i == 2) {
                        className += " columnCount";
                    } else if (i == 3) {
                        className += " racksPerShelf";
                    } else if (i == 4) {
                        className += " shelvesPerCluster";
                    }
                    target[i].after("<div class='"+className+"'><input style='margin-left:5px;vertical-align:text-bottom;' type='checkbox' class='allow_change'/><label>"+$.mage.__("Change current value")+"<span class='input_hint'>("+$.mage.__("changing this value leads to reset product location or totes barcode")+")</span></label></div>");
                }

                $("body").on("click", ".allow_change", function () {
                    var islocationcordinate = false;
                    if ($(this).parent(".changer").hasClass("rowCount") || $(this).parent(".changer").hasClass("columnCount") || $(this).parent(".changer").hasClass("racksPerShelf") || $(this).parent(".changer").hasClass("shelvesPerCluster")) {
                        islocationcordinate = true;
                    }
                    var target = $(this).parent(".changer").prev();
                    if (target.prop("disabled")) {
                        if (islocationcordinate) {
                            $("body").find(".rowCount").find("input").prop("checked", true).parent().prev().removeAttr("disabled");
                            $("body").find(".columnCount").find("input").prop("checked", true).parent().prev().removeAttr("disabled");
                            $("body").find(".racksPerShelf").find("input").prop("checked", true).parent().prev().removeAttr("disabled");
                            $("body").find(".shelvesPerCluster").find("input").prop("checked", true).parent().prev().removeAttr("disabled");
                        }
                        target.removeAttr("disabled");
                    } else {
                        if (islocationcordinate) {
                            $("body").find(".rowCount").find("input").prop("checked", false).parent().prev().attr("disabled", "disabled");
                            $("body").find(".columnCount").find("input").prop("checked", false).parent().prev().attr("disabled", "disabled");
                            $("body").find(".racksPerShelf").find("input").prop("checked", false).parent().prev().attr("disabled", "disabled");
                            $("body").find(".shelvesPerCluster").find("input").prop("checked", false).parent().prev().attr("disabled", "disabled");
                        }
                        target.attr("disabled", "disabled");
                    }
                });
            }

        });
    }
);
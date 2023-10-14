/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Pos
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

define([
    "mage/adminhtml/grid"
], function () {
    "use strict";
    return function (config) {
        var selectedProducts = config.selectedProducts,
            selectedProducts = $H(selectedProducts),
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000;
        $("warehouse_selected_products").value = Object.toJSON(selectedProducts);

        /**
         * Register Outlet Product
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerSelectedProduct(grid, element, checked)
        {
            if (checked) {
                if (element.positionElement) {
                    if (typeof element.up(2).getElementsByClassName("configurable")[0] != "undefined" || typeof element.up(2).getElementsByClassName("grouped")[0] != "undefined" || typeof element.up(2).getElementsByClassName("bundle")[0] != "undefined") {
                        element.positionElement.disabled = true;
                    } else {
                        element.positionElement.disabled = false;
                    }
                    selectedProducts.set(element.value, element.positionElement.value);
                }
            } else {
                if (element.positionElement) {
                    element.positionElement.disabled = true;
                }
                selectedProducts.unset(element.value);
            }
            $("warehouse_selected_products").value = Object.toJSON(selectedProducts);
            grid.reloadParams = {
                "selected_products[]": selectedProducts.keys()
            };
        }

        /**
         * Click on product row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function selectedProductRowClick(grid, event)
        {
            var trElement = Event.findElement(event, "tr"),
                isInput = Event.element(event).tagName === "INPUT",
                checked = false,
                checkbox = null;
            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, "input");
                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        /**
         * Change product position
         *
         * @param {String} event
         */
        function positionChange(event)
        {
            var element = Event.element(event);
            if (element) {
                var minVal = parseInt(element.min),
                    maxVal = parseInt(element.max),
                    qtyVal = parseInt(element.value);
                if (qtyVal <  minVal || maxVal < qtyVal) {
                    element.value = maxVal;
                }
            }
            if (element && element.checkboxElement && element.checkboxElement.checked) {
                selectedProducts.set(element.checkboxElement.value, element.value);
                $("warehouse_selected_products").value = Object.toJSON(selectedProducts);
            }
        }

        /**
         * Initialize Outlet product row
         *
         * @param {Object} grid
         * @param {String} row
         */
        function selectedProductRowInit(grid, row)
        {
            var checkbox = $(row).getElementsByClassName("checkbox")[0],
                position = $(row).getElementsByClassName("input-text")[0];
                // tempSelectedProduct = JSON.parse(Object.toJSON(selectedProducts));
            // if (typeof tempSelectedProduct[checkbox.value] != "undefined" && tempSelectedProduct[checkbox.value] != "") {
                // gridJsObject.setCheckboxChecked(checkbox, true);
                // // position.value = tempSelectedProduct[checkbox.value];
            // }
            if (checkbox && position) {
                checkbox.positionElement = position;
                position.checkboxElement = checkbox;
                if (typeof $(row).getElementsByClassName("configurable")[0] != "undefined" || typeof $(row).getElementsByClassName("grouped")[0] != "undefined" || typeof $(row).getElementsByClassName("bundle")[0] != "undefined") {
                    position.disabled = true;
                } else {
                    position.disabled = !checkbox.checked;
                }
                position.tabIndex = tabIndex++;
                Event.observe(position, "keyup", positionChange);
            }
        }

        gridJsObject.rowClickCallback = selectedProductRowClick;
        gridJsObject.initRowCallback = selectedProductRowInit;
        gridJsObject.checkboxCheckCallback = registerSelectedProduct;
        if (gridJsObject.rows) {
            gridJsObject.rows.each(function (row) {
                selectedProductRowInit(gridJsObject, row);
            });
        }
    };
});
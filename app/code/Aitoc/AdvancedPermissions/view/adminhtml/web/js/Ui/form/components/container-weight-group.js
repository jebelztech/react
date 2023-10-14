/*
 * Copyright © 2018 Aitoc. All rights reserved.
 */

define([
        'Magento_Ui/js/form/components/group',
        'uiRegistry'
    ], function (Group, uiRegistry) {
        'use strict';

        return Group.extend(
            {
                initElement: function (elem) {
                    this._super(elem);

                    if (this.shouldBeDisabled()) {
                        elem.disabled(true);
                    }

                    return this;
                },

                shouldBeDisabled: function () {
                    return !window.GlobalEnabled && !this.isNewProduct();
                },

                isNewProduct: function () {
                    var productDataSource = this.getProductDatasource();

                    return !productDataSource.data.product.stock_data.item_id;
                },

                getProductDatasource: function () {
                    return uiRegistry.get('product_form.product_form_data_source');
                }
            }
        );
    }
);

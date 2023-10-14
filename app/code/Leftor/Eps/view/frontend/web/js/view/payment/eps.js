
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'eps',
                component: 'Leftor_Eps/js/view/payment/method-renderer/eps-method'
            }
        );
        return Component.extend({});
    }
);
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
        type: 'cardondelivery',
        component: 'Mageplaza_Payment/js/view/payment/method-renderer/cardondelivery'
      }
    );
    return Component.extend({});
  }
);
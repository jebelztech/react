/*
 * Copyright © 2018 Aitoc. All rights reserved.
 */

var config = {
    config: {
        mixins: {
            'Magento_Ui/js/form/element/abstract': {  // Target module
                'Aitoc_AdvancedPermissions/js/abstract-mixin': true  // Extender module
            }
        }
    },
    map: {
        '*': {
            rolesTree: 'Magento_User/js/roles-tree',
            editInventoryFields: 'Aitoc_AdvancedPermissions/js/edit',
            'aitapTreeSuggest': 'Aitoc_AdvancedPermissions/js/custom-tree-suggest'
        }
    }
};

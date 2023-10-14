<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;

class EditorAttribute extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Construct
     *
     * @param Context $context
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Aitoc\AdvancedPermissions\Setup\UpgradeSchema::AITOC_ADVANCED_PERMISSIONS_EDITOR_ATTRIBUTE,
            \Aitoc\AdvancedPermissions\Api\Data\EditorAttributeInterface::ID
        );
    }
}

<?php
/**
 * Webkul Software.
 *
 *
 * @category  Webkul
 * @package   Webkul_DeliveryBoy
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */
namespace Webkul\DeliveryBoy\Plugin\Adminhtml\Demo;

class IgnoreApiCredentials
{

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Webkul\DeliveryBoy\Helper\Data $deliveryboyHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->request = $request;
        $this->deliveryboyHelper = $deliveryboyHelper;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Magento\Framework\Api\DataObAddDemoUserMessagejectHelper $subject
     * @param \Magento\Quote\Model\Cart\Totals $dataObject
     * @param array $data
     * @param string $interfaceName
     * @return void
     */
    public function beforeExecute(
        \Magento\Config\Controller\Adminhtml\System\Config\Save $subject
    ) {
        $section = $this->request->getParam('section');
        if ($section !== 'deliveryboy') {
            return null;
        }
        if ($this->deliveryboyHelper->isNotDemoUser()) {
            return null;
        }
        $groups = $this->request->getPost('groups');
        unset($groups['auth']);
        $this->request->setPostValue('groups', $groups);
        
        return null;
    }
}

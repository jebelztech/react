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

class AddDemoUserMessage
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
     * @param \Magento\Framework\Api\DataObjectHelper $subject
     * @param \Magento\Quote\Model\Cart\Totals $dataObject
     * @param array $data
     * @param string $interfaceName
     * @return void
     */
    public function afterExecute(
        \Magento\Config\Controller\Adminhtml\System\Config\Edit $subject,
        $result
    ) {
        $section = $this->request->getParam('section');
        if ($section !== 'deliveryboy') {
            return $result;
        }
        if ($this->deliveryboyHelper->isNotDemoUser()) {
            return $result;
        }
        $demoUserMessage = $this->deliveryboyHelper->getDemoUserMessage();
        $this->messageManager->addNoticeMessage($demoUserMessage);

        return $result;
    }
}

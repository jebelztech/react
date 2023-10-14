<?php

namespace Etail\Career\Block\Index;

class Detail extends \Magento\Framework\View\Element\Template
{
	protected $careers;
	protected $request;
	public function __construct(\Magento\Catalog\Block\Product\Context $context, \Magento\Framework\App\Request\Http $request, \Etail\Career\Model\Careers $careers, array $data = []) {
		$this->careers = $careers;
		$this->request = $request;
		parent::__construct($context, $data);
    }
    protected function _prepareLayout()
    {
        $career = $this->loadCareer();
        $this->pageConfig->getTitle()->set(__($career->getName()));  
        return parent::_prepareLayout();
    }
    /*public function execute()
    {

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }*/
    public function loadCareer() {
    	$id = $this->request->getParam('id');
    	$career = $this->careers->load($id);
    	return $career;
    	//echo "<pre>"; print_r($career->getData()); exit;
    }
    public function careerList() {
        return $this->careers->getCollection()->addFieldToFilter('status',1);
    }
}
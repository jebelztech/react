<?php

namespace Etail\Career\Block\Index;


class Index extends \Magento\Framework\View\Element\Template {

    public function __construct(\Magento\Catalog\Block\Product\Context $context, \Magento\Framework\App\Request\Http $request, \Etail\Career\Model\Careers $careers,array $data = []) {
    	$this->careers = $careers;
		$this->request = $request;
        parent::__construct($context, $data);

    }
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    public function loadCareer() {
    	$id = $this->request->getParam('id');
    	$career = $this->careers->create()->load($id);
    	return $career;
    	//echo "<pre>"; print_r($career->getData()); exit;
    }

}
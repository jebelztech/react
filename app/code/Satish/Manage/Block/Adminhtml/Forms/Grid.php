<?php
namespace Satish\Manage\Block\Adminhtml\Forms;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Satish\Manage\Model\formsFactory
     */
    protected $_formsFactory;

    /**
     * @var \Satish\Manage\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Satish\Manage\Model\formsFactory $formsFactory
     * @param \Satish\Manage\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Satish\Manage\Model\FormsFactory $FormsFactory,
        \Satish\Manage\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_formsFactory = $FormsFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_formsFactory->create()->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );


		
				$this->addColumn(
					'name',
					[
						'header' => __('Name'),
						'index' => 'name',
					]
				);
				
				$this->addColumn(
					'email',
					[
						'header' => __('Email'),
						'index' => 'email',
					]
				);
				
				$this->addColumn(
					'mobile',
					[
						'header' => __('Mobile'),
						'index' => 'mobile',
					]
				);
				
				$this->addColumn(
					'message',
					[
						'header' => __('Message'),
						'index' => 'message',
					]
				);
				
				$this->addColumn(
					'subject',
					[
						'header' => __('Subject'),
						'index' => 'subject',
					]
				);
				

						$this->addColumn(
							'status',
							[
								'header' => __('Status'),
								'index' => 'status',
								'type' => 'options',
								'options' => \Satish\Manage\Block\Adminhtml\Forms\Grid::getOptionArray5()
							]
						);

						

						$this->addColumn(
							'page_type',
							[
								'header' => __('Page Type'),
								'index' => 'page_type',
								'type' => 'options',
								'options' => \Satish\Manage\Block\Adminhtml\Forms\Grid::getOptionArray6()
							]
						);

						
				$this->addColumn(
					'created_at',
					[
						'header' => __('Date'),
						'index' => 'created_at',
						'type'      => 'datetime',
					]
				);

					


		
        //$this->addColumn(
            //'edit',
            //[
                //'header' => __('Edit'),
                //'type' => 'action',
                //'getter' => 'getId',
                //'actions' => [
                    //[
                        //'caption' => __('Edit'),
                        //'url' => [
                            //'base' => '*/*/edit'
                        //],
                        //'field' => 'id'
                    //]
                //],
                //'filter' => false,
                //'sortable' => false,
                //'index' => 'stores',
                //'header_css_class' => 'col-action',
                //'column_css_class' => 'col-action'
            //]
        //);
		

		
		   $this->addExportType($this->getUrl('manage/*/exportCsv', ['_current' => true]),__('CSV'));
		   $this->addExportType($this->getUrl('manage/*/exportExcel', ['_current' => true]),__('Excel XML'));

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

	
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->setMassactionIdField('id');
        //$this->getMassactionBlock()->setTemplate('Satish_Manage::forms/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('forms');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('manage/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_status->getOptionArray();

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('manage/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    ]
                ]
            ]
        );


        return $this;
    }
		

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('manage/*/index', ['_current' => true]);
    }

    /**
     * @param \Satish\Manage\Model\forms|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'manage/*/edit',
            ['id' => $row->getId()]
        );
		
    }

	
		static public function getOptionArray5()
		{
            $data_array=array(); 
			$data_array[0]='Pending';
			$data_array[1]='Checked';
            return($data_array);
		}
		static public function getValueArray5()
		{
            $data_array=array();
			foreach(\Satish\Manage\Block\Adminhtml\Forms\Grid::getOptionArray5() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);
			}
            return($data_array);

		}
		
		static public function getOptionArray6()
		{
            $data_array=array(); 
			$data_array[0]='Contact';
			$data_array[1]='Career';
            return($data_array);
		}
		static public function getValueArray6()
		{
            $data_array=array();
			foreach(\Satish\Manage\Block\Adminhtml\Forms\Grid::getOptionArray6() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);
			}
            return($data_array);

		}
		

}
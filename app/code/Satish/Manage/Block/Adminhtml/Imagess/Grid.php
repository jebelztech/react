<?php
namespace Satish\Manage\Block\Adminhtml\Imagess;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Satish\Manage\Model\imagessFactory
     */
    protected $_imagessFactory;

    /**
     * @var \Satish\Manage\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Satish\Manage\Model\imagessFactory $imagessFactory
     * @param \Satish\Manage\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Satish\Manage\Model\ImagessFactory $ImagessFactory,
        \Satish\Manage\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_imagessFactory = $ImagessFactory;
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
        $collection = $this->_imagessFactory->create()->getCollection();
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
					'link',
					[
						'header' => __('Link'),
						'index' => 'link',
					]
				);
				
				$this->addColumn(
					'sorting',
					[
						'header' => __('sorting'),
						'index' => 'sorting',
					]
				);
				

						$this->addColumn(
							'type',
							[
								'header' => __('Type'),
								'index' => 'type',
								'type' => 'options',
								'options' => \Satish\Manage\Block\Adminhtml\Imagess\Grid::getOptionArray14()
							]
						);

						

						$this->addColumn(
							'status',
							[
								'header' => __('Status'),
								'index' => 'status',
								'type' => 'options',
								'options' => \Satish\Manage\Block\Adminhtml\Imagess\Grid::getStatus()
							]
						);

						
				$this->addColumn(
					'created_at',
					[
						'header' => __('Date'),
						'index' => 'created_at',
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
        //$this->getMassactionBlock()->setTemplate('Satish_Manage::imagess/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('imagess');

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
     * @param \Satish\Manage\Model\imagess|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'manage/*/edit',
            ['id' => $row->getId()]
        );
		
    }

	
		static public function getOptionArray14()
		{
            $data_array=array(); 
			$data_array[0]='Home Slider';
            $data_array[1]='Brand';
			$data_array[3]='Home Category';
            return($data_array);
		}
		static public function getValueArray14()
		{
            $data_array=array();
			foreach(\Satish\Manage\Block\Adminhtml\Imagess\Grid::getOptionArray14() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);
			}
            return($data_array);

		}
		
		static public function getStatus()
		{
            $data_array=array(); 
			$data_array[1]='Enabled';
			$data_array[2]='Disabled';
            return($data_array);
		}
		static public function getValueArray15()
		{
            $data_array=array();
			foreach(\Satish\Manage\Block\Adminhtml\Imagess\Grid::getStatus() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);
			}
            return($data_array);

		}
		

}
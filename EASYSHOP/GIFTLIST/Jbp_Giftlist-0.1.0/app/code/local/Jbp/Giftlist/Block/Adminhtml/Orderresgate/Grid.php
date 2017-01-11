<?php
class Jbp_Giftlist_Block_Adminhtml_Orderresgate_Grid extends Mage_Adminhtml_Block_Widget_Grid{
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_grid_list_resgate');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);   
    }
    
    protected function _prepareCollection()
    {
        $idList = Mage::app()->getRequest()->getParam('id');
        $collection = Mage::getResourceModel('sales/order_collection');                
        $collection->addAttributeToFilter('jbp_giftlist_id', array('eq' => $idList));
        $collection->addAttributeToFilter('jbp_giftlist_resgate', array('eq' => 1));
        $resource = Mage::getSingleton('core/resource');
        
        $collection->getSelect()
        ->join(
            array('sogc' => $resource->getTableName('sales/order_grid')),
            'sogc.increment_id = main_table.increment_id'
        );
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
protected function _prepareColumns()
    {    
        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
            'filter_index'=>'sogc.increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'filter_index'=>'sogc.store_id',
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
            'filter_index'=>'sogc.created_at',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
            'filter_index'=>'sogc.billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
            'filter_index'=>'sogc.shipping_name',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
            'filter_index'=>'sogc.base_grand_total',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
            'filter_index'=>'sogc.grand_total',
        ));
        
        $this->addColumn('subtotal', array(
            'header' => Mage::helper('sales')->__('Subtotal'),
            'index' => 'subtotal',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));
        
        $this->addColumn('base_shipping_amount', array(
            'header' => Mage::helper('sales')->__('Custo de envio'),
            'index' => 'base_shipping_amount',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));
        
        $this->addColumn('base_discount_amount', array(
            'header' => Mage::helper('sales')->__('Desconto'),
            'index' => 'base_discount_amount',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));
        
        

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'filter_index' => 'sogc.status',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));
        
        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));
        
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/gridresgate', array('_current'=>true));
    }
    
}
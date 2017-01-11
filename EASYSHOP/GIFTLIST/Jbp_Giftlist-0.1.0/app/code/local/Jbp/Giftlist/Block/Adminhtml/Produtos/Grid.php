<?php
class Jbp_Giftlist_Block_Adminhtml_Produtos_Grid extends Mage_Adminhtml_Block_Widget_Grid{
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('produtos_grid');
        $this->setUseAjax(true);
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);   
    }
    
    protected function _prepareCollection()
    {
        $idList = Mage::app()->getRequest()->getParam('id');
        $resource = Mage::getSingleton('core/resource');
        
        $collection = Mage::getModel('catalog/product')
                            ->getCollection()
                            ->addAttributeToSelect('*');        
        $collection->getSelect()
                   ->join(
                        array('jbp'=> $resource->getTableName('jbpgiftlist_item')),
                        "jbp.jbpgi_product_id = entity_id AND jbp.jbpgi_list_id = {$idList}"
                        );
        
                   
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {    
        $this->addColumn('entity_id', array(
            'header'=> Mage::helper('jbp_giftlist')->__('ID'),
            'width' => '80px',
            'type'  => 'number',
            'index' => 'entity_id',
        ));
        
        $this->addColumn('name', array(
            'header'=> Mage::helper('jbp_giftlist')->__('Name'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'name',
        ));
        
        $this->addColumn('status', array(
            'header'=> Mage::helper('jbp_giftlist')->__('Status'),
            'width' => '80px',
            'type'  => 'text',
            'filter' => false,
            'renderer' => 'Jbp_Giftlist_Block_Adminhtml_Produtos_Grid_Renderer_Status'
        ));
        
 
        return parent::_prepareColumns();
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/gridprodutos', array('_current'=>true));
    }
    
}
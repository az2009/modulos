<?php
class Jbp_Giftlist_Block_Adminhtml_Giftlist_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    
    public function __construct(){
        parent::__construct();
        $this->setId('listMarriageGrid');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection()
    {
        $rs = Mage::getSingleton('core/resource');
        
        $collection = Mage::getModel('jbp_giftlist/list')->getCollection();
        
        $collection->getSelect()
        ->join(
            array('e' => $rs->getTableName('customer_entity')),
            'main_table.jbpgl_customer_id = e.entity_id'
        );
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns(){
        $this->addColumn('jbpgl_id', array(
            'header'    => Mage::helper('jbp_giftlist')->__('ID'),
            'align'     => 'center',
            'width'     => '50px',
            'index'     => 'jbpgl_id',
            'type'  	=> 'number',
        ));
        
        $this->addColumn('jbpgl_name_noivo', array(
            'header'    => Mage::helper('jbp_giftlist')->__('Noivo'),
            'align'     => 'center',
            'width'     => '50px',
            'index'     => 'jbpgl_name_noivo',
        ));
        
        $this->addColumn('jbpgl_name_noiva', array(
            'header'    => Mage::helper('jbp_giftlist')->__('Noiva'),
            'align'     => 'center',
            'width'     => '50px',
            'index'     => 'jbpgl_name_noiva',
        ));
        
        $this->addColumn('jbpgl_active', array(
            'header'    => Mage::helper('jbp_giftlist')->__('Ativado'),
            'align'     => 'center',
            'width'     => '50px',
            'index'     => 'jbpgl_active',
            'type'  	=> 'int',
        ));
        
        $this->addColumn('email', array(
            'header'    => Mage::helper('jbp_giftlist')->__('E-mail'),
            'align'     => 'center',
            'width'     => '50px',
            'index'     => 'email',
            'type'  	=> 'text',
        ));
        
        $this->addColumn('jbpgl_event_date', array(
            'header'    => Mage::helper('jbp_giftlist')->__('Data do evento'),
            'align'     => 'center',
            'width'     => '50px',
            'index'     => 'jbpgl_event_date',
            'type'  	=> 'date',
        ));
        
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('jbp_giftlist')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('jbp_giftlist')->__('Visualizar'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            ));
        
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
    
}
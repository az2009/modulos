<?php

class Jbp_Giftlist_Block_Adminhtml_Giftlist_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('jbp_giftlist_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('jbp_giftlist')->__('Menu'));
    }
    
    protected function _beforeToHtml()
    {
        $this->addTab('info_list', array(
            'label'     => Mage::helper('jbp_giftlist')->__('Detalhes'),
            'title'     => Mage::helper('jbp_giftlist')->__('Detalhes'),
            'content'   => $this->getLayout()->createBlock('jbp_giftlist/adminhtml_giftlist_edit_tab_form')->toHtml(),
        ));
        
        $this->addTab('grid_order_resgate', array(
            'label'     => Mage::helper('jbp_giftlist')->__('Pedidos resgatados'),
            'title'     => Mage::helper('jbp_giftlist')->__('Pedidos resgatados'),
            'content'   => $this->getLayout()->createBlock('jbp_giftlist/adminhtml_orderresgate')->toHtml(),
        ));
        
        $this->addTab('grid_order_credito', array(
            'label'     => Mage::helper('jbp_giftlist')->__('Pedidos de crédito'),
            'title'     => Mage::helper('jbp_giftlist')->__('Pedidos de crédito'),
            'content'   => $this->getLayout()->createBlock('jbp_giftlist/adminhtml_ordercredito')->toHtml(),
        ));
        
        $this->addTab('grid_order_produtos', array(
            'label'     => Mage::helper('jbp_giftlist')->__('Produtos'),
            'title'     => Mage::helper('jbp_giftlist')->__('Produtos'),
            'content'   => $this->getLayout()->createBlock('jbp_giftlist/adminhtml_produtos')->toHtml(),
        ));
         
        return parent::_beforeToHtml();
    }
}
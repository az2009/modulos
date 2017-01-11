<?php
class Jbp_Giftlist_Block_Adminhtml_Promo extends Mage_Adminhtml_Block_Widget_Grid_Container {
    
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_promo';
        $this->_blockGroup = 'jbp_giftlist';
        $this->_headerText = Mage::helper('jbp_giftlist')->__('Regras de promoção da lista de casamento');
        $this->_removeButton('save');
        $this->_removeButton('add');
        $this->_removeButton('reset');
        $this->_removeButton('delete');        
    }        
}
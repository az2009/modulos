<?php
class Jbp_Giftlist_Block_Adminhtml_Giftlist extends Mage_Adminhtml_Block_Widget_Grid_Container {
    
    public function __construct()
    {
        $this->_controller = 'adminhtml_giftlist';
        $this->_blockGroup = 'jbp_giftlist';
        $this->_headerText = Mage::helper('jbp_giftlist')->__('Lista de casamento');
        $this->_removeButton('save');
        $this->_removeButton('add');
        $this->_removeButton('reset');
        $this->_removeButton('delete');
        parent::__construct();
    }
    
    public function getHeaderText()
    {
        return Mage::helper('jbp_giftlist')->__('Informações da lista de casamento');
    }
    
}
<?php
class Jbp_Giftlist_Block_Adminhtml_Giftlist_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_giftlist';
        $this->_blockGroup = 'jbp_giftlist';        
        $this->_removeButton('save');
        $this->_removeButton('add');
        $this->_removeButton('reset');
        $this->_removeButton('delete');
    }
    public function getHeaderText()
    {
        return Mage::helper('jbp_giftlist')->__('Informações da lista de casamento');
    }
    
}
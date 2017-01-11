<?php
class Jbp_Giftlist_Block_Minhaslistas_Minhaslistas extends Jbp_Giftlist_Block_Giftlist {
    
    /**
     * Retorna todas as listas do usuário logado
     */
    public function getListsAll(){
        $collection = Mage::getModel('jbp_giftlist/list')
                            ->getCollection()
                            ->addFieldToSelect('*')
                            ->addFieldToFilter('jbpgl_customer_id', array('eq'=>$this->_getHelper()->getCustomer()->getId()));
        
        $collection->getSelect()->join(
                'jbpgiftlist_address_event',
                'main_table.jbpgl_event_address_id = jbpgiftlist_address_event.jbpgae_id',
                array('*'));
                            
        return $collection;
    }
    
    public function getListViewId(){
        return $this->getRequest()->getParam('id');
    }
    
    public function getCouponOfList(){
        $list = Mage::getModel('jbp_giftlist/list')->load($this->getListViewId());
        return $list;
    }
    
    
}

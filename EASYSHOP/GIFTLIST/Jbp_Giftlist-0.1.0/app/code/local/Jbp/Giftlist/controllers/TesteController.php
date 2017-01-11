<?php
class Jbp_Giftlist_TesteController extends Mage_Core_Controller_Front_Action {

    public function generatorAction(){
        echo $this->_getHelper()->getCustomer()->getCustomer()->getEmail();

    }
    
    public function getSaldoDisponivel(){
        $jbpgl_id = $this->_getHelper()->getList();
        $rs = Mage::getSingleton('core/resource');
        $collection = Mage::getModel('sales/order')
        ->getCollection()
        ->addAttributeToSelect('entity_id')
        ->addFieldToFilter(
            array('status'),
            array(
                array('credito_lista_casamento')
            )
            )
            ->addAttributeToFilter('jbp_giftlist_id', array('eq' => $jbpgl_id))
            ->addAttributeToFilter('jbp_giftlist_resgate', array('eq' => 0));
    
            $collectionSecond = Mage::getModel('sales/order')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('jbp_giftlist_id', array('eq' => $jbpgl_id))
            ->addAttributeToFilter('jbp_giftlist_resgate', array('eq' => 1));
            $collectionSecond->getSelect()->columns('SUM(main_table.base_grand_total) AS total_resgatado');
    
            $collection->getSelect()->columns('SUM(main_table.subtotal) + SUM(main_table.base_shipping_amount) AS qty_total, main_table.total_qty_ordered AS qty_total_qty');
    
            $val = $collection->getFirstItem()->getData('qty_total') - $collectionSecond->getFirstItem()->getData('total_resgatado');
            return $val;
    }
    
    public function _getHelper(){
        return Mage::helper('jbp_giftlist');
    }
    

}
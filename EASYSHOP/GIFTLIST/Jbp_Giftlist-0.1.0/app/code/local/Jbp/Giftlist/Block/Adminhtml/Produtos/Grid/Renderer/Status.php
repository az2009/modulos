<?php
class Jbp_Giftlist_Block_Adminhtml_Produtos_Grid_Renderer_Status 
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract  {
    
        public function render(Varien_Object $row){
			 return $this->_getValue($row);			
		}
    		
    	protected function _getValue(Varien_Object $row){    
	      $status = array();
	      $idList = Mage::app()->getRequest()->getParam('id');
	      $resource = Mage::getSingleton('core/resource');
	      
	      $idProd = $row->getData('jbpgi_product_id');
	      
	      $collection = Mage::getModel('sales/order')
	                           ->getCollection()
	                           ->addAttributeToFilter('jbp_giftlist_id', array('eq'=>$idList));
	      
          $collection->getSelect()
          ->join(
              array('soi' => 'sales_flat_order_item'),
              "(soi.order_id = main_table.entity_id) AND soi.product_id = {$idProd}"
              );
          
          foreach($collection as $item){
              if($item->getData('jbp_giftlist_resgate') == 1){
                  $status[] = '<span>&#9745;</span> Resgatado';
              }else if($item->getData('jbp_giftlist_resgate') == 0){
                  $status[] = '<span>&#9745;</span> Recebido';
              }
          }
          
          return implode(' | ', $status);
          
    	}
        
}
<?php
class Jbp_Giftlist_Block_Adminhtml_Giftlist_Edit_Tab_Renderer_Status 
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract  {
    
        public function render(Varien_Object $row){
			 return $this->_getValue($row);			
		}
    		
    	protected function _getValue(Varien_Object $row){    
	      $status = $row->getData('jbpgl_active');
	      
	      if($status){
	          return 'Ativado';
	      }
	          return 'Desativado';	      
    	}
        
}
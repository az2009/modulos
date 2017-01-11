<?php
class HC_Theme_Model_Theme extends Mage_Core_Model_Abstract {
    
    protected function _construct(){
        parent::_construct();
        $this->_init('hc_theme/theme');    
    }
    
}
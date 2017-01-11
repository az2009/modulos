<?php
class Jbp_Giftlist_Model_Mysql4_Address_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct(){
        $this->_init('jbp_giftlist/address');
        parent::_construct();
    }

}
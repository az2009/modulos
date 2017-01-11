<?php
class Jbp_Giftlist_Model_Mysql4_Address extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct(){
        $this->_init('jbp_giftlist/address', 'jbpgae_id');
    }

}
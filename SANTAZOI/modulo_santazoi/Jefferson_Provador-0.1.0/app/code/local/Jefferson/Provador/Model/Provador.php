<?php
    class Jefferson_Provador_Model_Provador extends Mage_Core_Model_Abstract{

        protected function _construct(){
            parent::_construct();
            $this->_init('provador/provador');
        }
    }
?>
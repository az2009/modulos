<?php
class HC_ShippingRestriction_Helper_Data extends Mage_Core_Helper_Abstract {
    
    /**
     * Pega o CEP min
     */
    public function getPosteCodeMin(){
        return Mage::getStoreConfig('shippingrestrictionhc_config/shippingrestrictionhc_general/feedgoogleshopping_faixa_min');
    }
    
    /**
     * Pega o CEP  Max
     */
    public function getPosteCodeMax(){
        return Mage::getStoreConfig('shippingrestrictionhc_config/shippingrestrictionhc_general/feedgoogleshopping_faixa_max');
    }
    
    /**
     * Verifica se o módulo esta ativo
     */
    public function _isEnabled(){
        return Mage::getStoreConfig('shippingrestrictionhc_config/shippingrestrictionhc_general/feedgoogleshopping_enabled');
    }
    
}
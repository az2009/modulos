<?php
class HC_ShippingRestriction_Model_Observer {
    
    /**
     * Pega o CEP do usuário e executa a validação da regra de envio
     * @param unknown $observer
     */
    public function checkPostCode($observer){
        
        if(!$this->_helper()->_isEnabled()) return;
        
        $quote = Mage::getSingleton('checkout/cart')->getQuote();            
        $post = $quote->getShippingAddress()->getData('postcode');
        
        if(($post >= $this->_helper()->getPosteCodeMin()) && ($post <= $this->_helper()->getPosteCodeMax())){
            $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();        
            foreach($methods as $k => $v){
                $this->setStoreConfig("carriers/{$k}/active", 0);
            }                        
        }
    }
    
    /**
     * Desativa os módulos de envio
     * @param unknown $path
     * @param unknown $value
     * @return HC_ShippingRestriction_Model_Observer
     */
    public function setStoreConfig($path, $value)
    {
        $store  = Mage::app()->getStore();
        $store->setConfig($path, $value);
        return $this;
        $code   = $store->getCode();
        $config = Mage::getConfig();
        $store->setConfig("stores/$code/$path", $value);
        return $this;
    }
    
    /**
     * Retorna uma instancia da helper
     */
    public function _helper(){
        return Mage::helper('hc_shippingrestriction');
    }
    
}

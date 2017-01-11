<?php
class Jbp_Featuredbycategory_Model_Observer {
    
    protected $name = 'IDPRODUCT';
    protected $id   = null;
    
    public function setExecuteClickview($observer){
        $this->id = $observer->getProduct()->getId();
        self::setCookie($this->name, $this->id);
    }
    
    public function setCookie($name, $value){
        return Mage::getSingleton('core/cookie')->set($name, $value);
    }
    
}
<?php 
class Jbp_Featuredbycategory_Helper_Data extends Mage_Core_Helper_Abstract {
 
    public function getCookie($name){
        return Mage::getSingleton('core/cookie')->get('IDPRODUCT');
    }
    
    public function getAttributeProductsRandom(){
        return Mage::getStoreConfig('catalog/jbp_featuredbycategory_product_clickview/jbp_theme_attribute');
    }
   
}
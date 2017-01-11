<?php
class HC_FeedGoogleShopping_Helper_Zoom extends Mage_Core_Helper_Abstract {
    
    /**
     * Retorna as config
     */
    public function getCfg($config){
        return Mage::getStoreConfig("feedgoogleshopping_config/feedzoom_attributes/{$config}");
    }
    
    /**
     * Trata o preço especial do produto
     * Retorna o preço padrão caso o produto
     * não tenha preço especial
     * @param unknown $item
     */
    public function getPriceSpecial(Mage_Catalog_Model_Product $item){
        if(!empty($item->getSpecialPrice())){
            return $item->getSpecialPrice();
        }
        return $item->getPrice();
    }
    
    /**
     * Formata a categoria para o  padrão zoom
     * Pega a primeira como departamento e a última como subdepartamento
     * @param unknown $cat
     * @return string
     */
    public function getCategoryZoom($cat){
        $cat = explode('>', $cat);
        return "<departamento>".$cat[0]."</departamento>"."<subdepartamento>".end($cat)."</subdepartamento>";
    }
    
}
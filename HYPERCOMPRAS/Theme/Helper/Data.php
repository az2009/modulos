<?php
class HC_Theme_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Formata o CPF/CNPJ
     */
    public function getTaxvat($taxvat){

        $taxvat = preg_replace("/[^0-9]/", "", $taxvat);

        if(strlen($taxvat) == 11 ){

            return "CPF: <strong>".$taxvat = substr($taxvat, 0,3).'.***.***-'.substr($taxvat,-2)."</strong>";

        }elseif(strlen($taxvat) == 14 ){

            return "CNPJ: <stromg>".$taxvat = substr($taxvat, 0,2).'.***.***/****-'.substr($taxvat,-2)."</strong>";

        }else{
            return $taxvat = substr($taxvat, 0,2).'.***.'.substr($taxvat,-2);
        }

    }

    public function getAttributeProductsRandom(){
        return Mage::getStoreConfig('catalog/hc_theme_product_related/hc_theme_attribute');
    }

    public function getStatusProductsRandom(){
        return Mage::getStoreConfig('catalog/hc_theme_product_related/hc_theme_enabled');
    }

    public function getSizeProductsRandom(){
        return Mage::getStoreConfig('catalog/hc_theme_product_related/hc_theme_products_size');
    }

}
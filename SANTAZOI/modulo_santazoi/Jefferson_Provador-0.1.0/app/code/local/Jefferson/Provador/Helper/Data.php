<?php
class Jefferson_Provador_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Pega o status do módulo provador
     * @return unknown
     */
    public function getEnabled(){
        $config = Mage::getStoreConfig('provador_options/provador_general/provador_enabled');
        return $config;
    }


    /**
     * Pega o método de pagamento ativos
     * @return unknown
     */
    public function getPayment(){
        $config = Mage::getStoreConfig('provador_options/provador_general/provador_payment_options');
        return $config;
    }

    /**
     * Pega o sku do custom options
     * @return unknown
     */
    public function getSku(){
        $config = Mage::getStoreConfig('provador_options/provador_general/provador_sku_custom_options');
        return $config;
    }


    /**
     * Pega o SKU do Custom options
     * @param unknown $option_id
     * @return NULL
     */
    public function getSkuCustomOption($option_id){

        $sku = null;

        $collection = Mage::getResourceModel('catalog/product_option_value_collection')
                            ->addFieldToFilter('option_id', $option_id)
                            ->getValues(Mage::app()->getStore()->getStoreId());

        foreach ($collection as $item){
            $sku = strtolower(trim($item->getSku()));
        }
        return $sku;
    }

    /**
     * Adiciona as opções custom options
     * @param unknown $params
     * @param unknown $product
     * @return array|unknown
     */
    public function setCustomOptions($params, $product){

        if(array_key_exists('options', $params) == false){
            if(array_key_exists('prova', $params)){
                foreach($product->getOptions() as $option){
                    if($option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX){
                        foreach ($option->getValues() as $oValue) {
                            if($oValue->getSku() == $this->getSku()){
                                $array['options'][$oValue->getOptionId()][]= $oValue->getId();
                            }
                        }
                    }
                }

                return array_merge_recursive($array, $params );
           }
        }

        return $params;

    }







}


?>
<?php

class Jefferson_Provador_Block_Provador extends Mage_Catalog_Block_Product_Abstract{

    /**
     * Pega o template do botão para lista de produtos sem configuração
     * @param unknown $products
     * @return unknown
     */
    public function getButtonAddToProv($products){

        $helper = Mage::helper('provador');

        $products = Mage::getModel('catalog/product')->load($products->getId());

        if($helper->getEnabled()){
            if($this->getOptionId($products)){
                $template = $this->setTemplate('jefferson/santazoi/addtocartlist.phtml')
                                 ->setData('product', $products)
                                 ->toHtml();
                return $template;
            }
        }
    }

    /**
     * Verifica se o produto esta disponpivel para prova
     * @param unknown $product
     * @return boolean
     */
    public function getOptionId($product){
        $helper = Mage::helper('provador');
        $_product = Mage::getModel('catalog/product')->load($product->getId());
        foreach ($_product->getOptions() as $_option) {
            if($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX){
                foreach ($_option->getValues() as $_value) {
                    $option_id = $_value->getOptionId();
                }
            }
        }

        if($helper->getSkuCustomOption($option_id) ==  $helper->getSku()){
            if($_product->isSaleable()){
                return true;
            }
        }

        return false;
    }

}

?>
<?php
class Jbp_Giftlist_Block_Product_View extends Mage_Catalog_Block_Product_View {


    public function getShipping($product, $params){

        $cal  = new EcomDev_ProductPageShipping_Model_Api;

        $data = array(
            'product'  => $product,
            'qty'  => $params['qty'],
            'postcode' => $this->_getHelper()->getIdAddressListMarriageBySession()->getData('postcode')
        );


        foreach($cal->returnshipping(json_encode($data,true)) as $item);
        return $item;

    }

    public function teste(){
        echo "teste";
    }

    public function _getHelper(){
        return Mage::helper('jbp_giftlist');
    }

}
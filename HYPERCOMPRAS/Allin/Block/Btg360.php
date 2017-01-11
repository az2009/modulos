<?php
class HC_Allin_Block_Btg360 extends Mage_Core_Block_Template {

    /**
     * Dispara a tag quando o cliente visualizar o produto
     * @return void|a
     */
    public function getEventProductView(){

        if(!$this->_helperbtg()->getConfigEventProductView()) return;

        if(Mage::registry('current_product')){

            $productId = Mage::registry('current_product')->getId();

            if(empty($productId)) return;


                $product = Mage::getModel('catalog/product')->load($productId);

                $itens = '[{
                     id: "'.$product->getSku().'",
                     name: "'.$this->_helperbtg()->removeQuote($product->getName()).'",
                     price: "'.Mage::helper('core')->currency($product->getFinalPrice(), true, false).'",'.
                     $this->_helperbtg()->formatCategory($product->getData('feed_google_shopping'))
                     .'brand: "'.$product->getAttributeText('manufacturer').'",
                  }]';

                $this->setData('evento','product');

                return $this->getBody($itens);
        }

    }


    /**
     * Dispara a tag quando o cliente adicionar produto ao carrinho
     * @return void|a
     */
    public function getEventProductCart(){

        if(!$this->_helperbtg()->getConfigEventAddProductCart()) return;

            $itens = null;

            $additional = null;

            $quote = Mage::getSingleton('checkout/session');


            if($quote->getQuote()->getItemsCount()){


                $quote = $quote->getQuote()->getAllVisibleItems();

                $additional = "itensCart = [];";

                foreach($quote as $item){

                    $product = Mage::getModel('catalog/product')->load($item->getProductId());

                    $additional .= "\n\n itensCart.push({
                                        id: '".$product->getSku()."',
                                        name: '".$this->_helperbtg()->removeQuote($product->getName())."',
                                        price: '".Mage::helper('core')->currency($product->getFinalPrice(), true, false)."',
                                        ".$this->_helperbtg()->formatCategory($product->getData('feed_google_shopping'))
                                        ."brand: '".$product->getAttributeText('manufacturer')."',
                                  });\n\n";
                }



                $this->setData('evento','cart');

                return $this->getBody('itensCart',$additional);
            }

    }

    /**
     * Dispara a tag caso o cliente esteja logado
     */
    public function getTrackingEventCustomer(){

        if(!$this->_helperbtg()->getConfigEventCustomerLoggin()) return;

        $customer = Mage::getSingleton('customer/session');

        if($customer->isLoggedIn()){
            $customerEmail = $customer->getCustomer()->getEmail();

            $itens = '[{ email: "'.$customerEmail.'" }]';

            $this->setData('evento','client');

            return $this->getBody($itens);
        }
    }



    /**
     * Dispara a tag quando o cliente finalizar a compra
     * @return void|a
     */
    public function getTrackingPlaceOrder(){

        if(!$this->_helperbtg()->getConfigEventPlaceOrder()) return;

        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();

        if(empty($orderId)) return;

        $order = Mage::getModel('sales/order')->load($orderId);

        $items = $order->getAllVisibleItems();

        $additional = "itensOrder = [];";

        foreach($items as $item){

            $product = Mage::getModel('catalog/product')->load($item->getProductId());

            $additional .= "\n\n itensOrder.push({
                            transactionId: '".$order->getData('increment_id')."',
                            id: '".$product->getSku()."',
                            name: '".$this->_helperbtg()->removeQuote($product->getName())."',
                            price: '".Mage::helper('core')->currency($product->getFinalPrice(), true, false)."',".
                            $this->_helperbtg()->formatCategory($product->getData('feed_google_shopping'))
                            ."brand: '".$product->getAttributeText('manufacturer')."',
                      });\n\n";
        }


        $this->setData('evento','transaction');

        return $this->getBody('itensOrder',$additional);

    }

    /**
     * Retorna o body da chamada js do bt360
     * @param unknown $itens | Itens a serem enviados
     * @param unknown $additional | JS Adicional
     * @return a string do evento formatado
     */
    public function getBody($itens, $additional = null){

        $body = null;

        $body = $additional;

        $body .= "Btg360.add({
                    account: '5060:1',
                    event: '".$this->getData('evento')."',
                    itens: {$itens}
                });";

        if($this->_helperbtg()->getConfigStateDebug()){
            $body .= "Btg360.debug();";
        }

        return $body;
    }


    /**
     * Retorna uma instacia da helperbtg
     */
    public function _helperbtg(){
        return Mage::helper('hc_allin/btg360');
    }
}
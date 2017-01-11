<?php
class Jbp_Giftlist_Helper_Rules extends Magestore_RewardPoints_Helper_Price {

    public $keyValue = null;

    /**
     * Inicia a variável para validar o carrinho
     */
    public function __construct(){
        $this->paramkey = 'listcasamento';
    }

    /**
     * Verifica se existe algum produto de prova no carrinho
     * return string
     */
    public function checkItemCartListOnly(){
        $p = array();
        $cart = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
        if(count($cart)){
            foreach ($cart as $product){
                foreach ($product->getOptions() as $opt) {
                    $value = unserialize($opt->getData('value'));
                    if(array_key_exists($this->paramkey, $value)){
                        $p[] = $value[$this->paramkey];
                    }
                }
            }

            if(count($p)){
                $this->keyValue = $p[0];
                foreach($p as $i){
                    if($i != $this->keyValue) return false;
                }
            }
            return true;
        }
        return 'cartempty';
    }

    /**
     * Verifica se o produto antes de adicionar ao carrinho
     * return string
     */
    public function checkItemCartBeforeAddListOnly($params){
        $p = null;
        $t = null;
        if(count($params)){
            if(array_key_exists($this->paramkey, $params)){
                $p = $params[$this->paramkey];
            }
            $t = $this->checkItemCartListOnly();
            if($t === 'cartempty')return true;

            if(($p == $this->keyValue) && $t){
                return true;
            }
        }
        return;
    }


    /**
     * Metodo disparado na tela do checkout verifica se existe dois tipos de produtos no carrinho
     * @return boolean
     */
    public function checkItemCartCheckout(){
        $p = array();
        $pnot = array();
        $cart = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
        if(count($cart)){
            foreach ($cart as $product){
                foreach ($product->getOptions() as $opt) {
                    if($opt->getData('code') == 'info_buyRequest'){
                        $value = unserialize($opt->getData('value'));
                        if(array_key_exists($this->paramkey, $value)){
                            $p[] = $value['product'];
                        }else{
                            $pnot[] = $value['product'];
                        }
                    }
                }
            }
            if(count($p) > 0 && count($pnot) > 0){
                return  true;
            }else{
                return  false;
            }
        }
    }

    /**
     * Metodo disparado na tela do checkout verifica se existe apenas produtos de troca no carrinho
     * @return boolean
     */
    public function checkItemCartCheckoutOnlyEasypoints(){
        $p = array();
        $pnot = array();
        $cart = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
        if(count($cart)){
            foreach ($cart as $product){
                foreach ($product->getOptions() as $opt) {
                    $value = unserialize($opt->getData('value'));
                    if(array_key_exists($this->paramkey, $value)){
                        $p[] = $value['product'];
                    }else{
                        $pnot[] = $value['product'];
                    }
                }
            }
            if(count($p) > 0 && count($pnot) == 0){
                return  true;
            }
        }
    }

    /**
     * Retorna o Id do endereço da lista de casamento
     * @return boolean
     */
    public function getIdAddressListMarriage(){
        if($this->getIdListMarriage()){
            $id = $this->getIdListMarriage();
            return Mage::getModel('jbp_giftlist/list')->load($id)->getData('jbpgl_event_shipping_id');
        }

    }


    /**
     * Retorna o Id da lista de casamento
     * @return boolean
     */
    public function getIdListMarriage(){
        $p = array();
        $pnot = array();
        $cart = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
        if(count($cart)){
            foreach ($cart as $product){
                foreach ($product->getOptions() as $opt) {
                    $value = unserialize($opt->getData('value'));
                    if(array_key_exists($this->paramkey, $value)){
                        $id = $value[$this->paramkey];
                        return $id;
                    }
                }
            }
        }
    }
}
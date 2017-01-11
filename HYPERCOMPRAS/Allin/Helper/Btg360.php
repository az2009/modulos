<?php
class HC_Allin_Helper_Btg360 extends Mage_Core_Helper_Abstract {


    public function getConfigEventGeneral(){
        return Mage::getStoreConfig('allin_trackevent/allin_user/allin_evento_geral');
    }

    public function getConfigEventAddProductCart(){
        if(!$this->getConfigEventGeneral()) return;
        return Mage::getStoreConfig('allin_trackevent/allin_user/allin_evento_add_cart_produto');
    }

    public function getConfigEventProductView(){
        if(!$this->getConfigEventGeneral()) return;
        return Mage::getStoreConfig('allin_trackevent/allin_user/allin_evento_produto');
    }

    public function getConfigEventPlaceOrder(){
        if(!$this->getConfigEventGeneral()) return;
        return Mage::getStoreConfig('allin_trackevent/allin_user/allin_evento_finalizar_pedido');
    }

    public function getConfigEventCustomerLoggin(){
        if(!$this->getConfigEventGeneral()) return;
        return Mage::getStoreConfig('allin_trackevent/allin_user/allin_evento_cliente');
    }

    public function getConfigStateDebug(){
        if(!$this->getConfigEventGeneral()) return;
        return Mage::getStoreConfig('allin_trackevent/allin_user/allin_evento_debug');
    }

    public function removeQuote($var){
        $var = str_replace("'", '', str_replace('"', '', $var));
        return $var;
    }
    

    public function formatCategory($string){
        $string = explode('>', $string);        
        $x   = 0;
        $tag = null;            
        
        while($x != 3){
           
            if($x == 0){                    
                $tag .=  (!empty($string[0])) ?  "department: '{$string[0]}'," : $tag .= "department: 'null'," ;                    
            }elseif($x == 1){
                $tag .=  (!empty($string[count($string) -2])) ?  "category: '{$string[count($string) -2]}'," : $tag .= "category: 'null'," ;
            }elseif($x == 2){
                $tag .=  (!empty($string[count($string) -1])) ?  "subcategory: '".end($string)."'," : $tag .= "subcategory: 'null'," ;
            }
                        
            $x++;
        }        
        return $tag;
        
    }
    
    
}

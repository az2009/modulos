<?php 
class HC_Allin_Helper_Data extends Mage_Core_Helper_Abstract {
    
    /**
     * Retorna o token configurado no admin
     */
    public function getToken(){
        return Mage::getStoreConfig('allin_config/allin_general/allin_token');
    }
    
    /**
     * Verifica se esta configurado para enviar via Magento
     */
    public function getMethodSend(){
        return Mage::getStoreConfig('allin_config/allin_general/allin_magento_send');
    }
    
    /**
     * Retorna a configuração de habiilitação do evento novos usuarios
     */
    public function getEnabledEventNewUser(){
        return Mage::getStoreConfig('allin_trackevent/allin_user/allin_enabled');
    }
    
    
    /**
     * Retorna um array em formato query string
     * @param unknown $data
     * @return string
     */
    public function prepareArray($data){
        return http_build_query($data);
    }
    
    /**
     * convert a string em base64
     * @param unknown $data
     */
    public function convertBase64($data){
        return base64_encode($data);
    }
    
    /**
     * Retorna um ID único para o template 
     * @return string
     */
    public function getCodeId(){
        return md5(rand(0,9999).mt_rand(99999,9999999).time());
    }
    
    /**
     * Retorna o usuário para gerar a Token
     */
    public function getUser(){   
        return Mage::getStoreConfig('allin_config/allin_general/allin_user');
    }
    
    /**
     * Retorna a senha para gerar a Token
     */
    public function getPass(){
        return Mage::getStoreConfig('allin_config/allin_general/allin_pass');
    }
    
    /**
     * Grava o log
     */
    public function registerLog($msg){
        if($this->isAdmin()){
            Mage::throwException($msg);
        }
        Mage::log($msg,null,'logAllin.log');
    }
    
    public function isAdmin(){
        return Mage::app()->getStore()->isAdmin();
    }
} 
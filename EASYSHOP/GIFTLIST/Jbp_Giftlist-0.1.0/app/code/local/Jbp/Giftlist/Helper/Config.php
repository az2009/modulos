<?php
class Jbp_Giftlist_Helper_Config extends Mage_Core_Helper_Abstract {
    
    
    /**
     * Retorna as config do módulo
     * @param unknown $config
     */
    public function getConfig($config){
        return Mage::getStoreConfig("jbp_giftlist_config/".trim($config));
    }
    
    public function getSizeUpload(){
        return (int)$this->getConfig('jbp_giftlist_general_image/size_upload');
    }
    
    public function getMsgDefault(){
        return (string)$this->getConfig('jbp_giftlist_general_config/msg_default');
    }
    
    public function getWidthImage(){
        return (int)$this->getConfig('jbp_giftlist_general_image/jbp_giftlist_size_image_width');
    }
    
    public function getHeigthImage(){
        return (int)$this->getConfig('jbp_giftlist_general_image/jbp_giftlist_size_image_heigth');
    }
    
    public function getImageDefault(){
        return (string)$this->getConfig('jbp_giftlist_general_image/jbp_giftlist_image_default');        
    }
    
    public function getEmailSender(){
        return (string)$this->getConfig('jbp_giftlist_general_config/email_default_remetente');
    }
    
    public function isEnabled(){
        return (string)$this->getConfig('jbp_giftlist_general_config/enable');
    }
    
}
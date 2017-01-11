<?php
class HC_FeedGoogleShopping_IndexController extends Mage_Core_Controller_Front_Action {

    const TEXT_XML = "text/xml";
    
    
    public function _construct(){
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        ini_set ('mysql.connect_timeout ',  0);
        ini_set ('default_socket_timeout',  0);
        parent::_construct();
    }
    
    /**
     * Gera o xml
     * Chamada a ser executa pelo cron
     */
    public function runAction(){
        
        /**
         * Gera o Feed do Google Shopping
         * @var unknown
         */
        $feed = Mage::getSingleton('hc_feedgoogleshopping/feed');
        $feed->feedGenerate();  
        
        /**
         * Gera o Feed do Zoom
         * @var unknown
         */
        $feedZoom = Mage::getModel('hc_feedgoogleshopping/feedzoom');
        $feedZoom->feedGenerate();        
        die();
        
    }

    /**
     * Gera e efetua o download do xml
     */
    public function indexAction(){

        if(!$this->_helper()->getFeedConfig('feedgoogleshopping_general/feedgoogleshopping_enabled')){
             Mage::getSingleton('admin/session')->addError('Ative o módulo para gerar o Feed');
             $this->_redirectReferer();
            return;
        }

        $feed = Mage::getSingleton('hc_feedgoogleshopping/feed');
        $feed->feedGenerate();
        $this->_helper()->displayFile("googleShopping.xml", self::TEXT_XML);
        die();
    }

    
    public function feedzoomAction(){
        
        if(!$this->_helper()->getFeedConfig('feedgoogleshopping_general/feedzoom_enabled')){
            Mage::getSingleton('admin/session')->addError('Ative o módulo para gerar o Feed');
            $this->_redirectReferer();
            return;
        }
        
        $feed = Mage::getModel('hc_feedgoogleshopping/feedzoom');
        $feed->feedGenerate();
        $this->_helper()->displayFile("xmlzoom.xml", self::TEXT_XML);
        die();        
    }
    

    /**
     * Retorna uma instancia da helper do módulo
     */
    public function _helper(){
        return Mage::helper('hc_feedgoogleshopping');
    }
    
    
    /**
     * Retorna uma instancia da helper do módulo zoom
     */
    public function _helperZoom(){
        return Mage::helper('hc_feedgoogleshopping/zoom');
    }

    
    


}
<?php 
class HC_Allin_Model_Api extends Mage_Core_Model_Abstract {
    
    private $htmlId;
    
    protected function _construct(){
        parent::_construct();
        $this->_init('hc_allin/api');
    }
    
    /**
     * Cria um template HTML temporario
     * @param string $data
     */
    public function createHtml($param = array()){

        $data = array(
            'dados' => Mage::helper('core')->jsonEncode($param)
        );      
    
        $http = $this->getRequest();
    
        $http->write(
            Zend_Http_Client::POST,
            $this->getApiUrl('cadastrar_html'),
            '1.1',
            array(),
            $this->_helper()->prepareArray($data)
            );
    
        $this->htmlId = Mage::helper('core')->jsonDecode($http->read())['html_id'];
        
        if($this->htmlId == 'Ticket nao confere!'){
            $this->_helper()->registerLog('TOKEN Expirado Allin: Ticket nao confere');
            return;
        }

        return $this;
    }
    
    /**
     * Dispara o e-mail através da API
     */
    public function sendEmail($param = array()){
        
        $data = array(
            'dados' => Mage::helper('core')->jsonEncode($param)
        );
        
        $http = $this->getRequest();
        
        $http->write(
            Zend_Http_Client::POST,
            $this->getApiUrl('enviar_email'),
            '1.1',
            array(),
            $this->_helper()->prepareArray($data)
            );
        
        return $http->read();
    }
    
    /**
     * Retorna a url da API
     * @param unknown $action
     * @return string
     */
    public function getApiUrl($action){
        return 'http://transacional01.postmatic.com.br/api/?method='.$action.'&output=json&token='.$this->getToken();
    }
    
    /**
     * Retorna o token de acesso da API
     */
    public function getToken(){
        
        $http = $this->getRequest();
        
        $http->write(Zend_Http_Client::GET, $this->getApiUrlToken(), '1.1', array(), '');
        
        $data = $http->read();
        
        $token = Mage::helper('core')->jsonDecode($data)['token'];
        
        if($token == 'Usuario e senha invalidos!'){            
            $this->_helper()->registerLog('TOKEN Allin: Usuario e senha invalidos!');
            return;
        }
        
        return $token; 
    }
    
    public function getApiUrlToken(){
        return 'http://transacional01.postmatic.com.br/api/?method=get_token&output=json&username='.$this->_helper()->getUser().'&password='.$this->_helper()->getPass();
    }
    
    /**
     * Retorna uma instancia da helper do modulo
     */
    public function _helper(){
        return Mage::helper('hc_allin');
    }
    
    /**
     * Monta uma requisição padrão
     * @return Varien_Http_Adapter_Curl
     */
    public function getRequest(){        
        $http = new Varien_Http_Adapter_Curl();        
        $config = array(
            'timeout' => 180,
            'header'  => false
        );        
        $http->setConfig($config);        
        return $http;
    }
    
    /**
     * Retorna o id do template HTML
     */
    public function getHtmlId(){        
        return $this->htmlId;
    }
}








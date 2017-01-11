<?php
class HC_Allin_Block_Allin extends Mage_Core_Block_Template {
      
    /**
     * Verifica qual o tipo de evento e executa o mesmo
     * @param unknown $event
     */
    public function isEvento(){
        
        switch ($this->getData('evento')){
            case 'new_customer':
                return $this->sendNewUser($this->getData('customer_id'));            
            break;
        }       
    }
    
    /**
     * Retorna os dados para o evento novo usuário
     * @param unknown $customerId
     */     
    public function sendNewUser($customerId){
        $customer = Mage::getModel('customer/customer')->load($customerId);              
        $data = array(
             'evento' => $this->jsQuoteEscape($this->_helper()->getNameEventNewUser()),
             'nm_email' => $this->jsQuoteEscape($customer->getEmail()),
             'nm_celular' => $this->jsQuoteEscape($this->getTelephone($customer)),
             'vars' => array(
                 'nome' => $this->jsQuoteEscape($customer->getFirstname().' '.$customer->getLastname())
             ),
             'lista' => array(
                 'nm_lista' => $this->jsQuoteEscape($this->_helper()->getNameListNewUser()),
                 'atualizar' => '1',
                 'nome' => $this->jsQuoteEscape($customer->getFirstname()).' '.$this->jsQuoteEscape($customer->getLastname()),
                 'dt_cadastro' => array(
                     'valor' => date('d/m/y h:i:s'),
                     'atualizar' => '0'
                 )
             )
         );         
         return Mage::helper('core')->jsonEncode($data);
    }    
    
    /**
     * Retorna o telephone do usuário
     * @param unknown $customer
     */
    public function getTelephone(Mage_Customer_Model_Customer $customer){
        foreach ($customer->getAddresses() as $address) {
            if(!empty($address->getData('telephone'))){
                $phone = "55".preg_replace("/[^A-Za-z0-9]/", "",$address->getData('telephone'));
            }            
        }
        return $phone;
    }
    
    public function _helper(){
        return Mage::helper('hc_allin');
    }
    
    
}
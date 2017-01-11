<?php
class Jbp_Giftlist_Block_Checkout_Shipping extends TM_FireCheckout_Block_Checkout_Shipping {
    
    public function getAddressesHtmlSelect($type)
    {        
        if ($this->isCustomerLoggedIn()) {
            $labelIn = null;
            $options = array();
    
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                );
            }
    
            $addressId = $this->getAddress()->getCustomerAddressId();
            if (empty($addressId)) {
                if ($type=='billing') {
                    $address = $this->getCustomer()->getPrimaryBillingAddress();
                } else {
                    $address = $this->getCustomer()->getPrimaryShippingAddress();
                }
                if ($address) {
                    $addressId = $address->getId();
                }
            }
    
            $labelIn = Mage::helper('checkout')->__('New Address');
            
            //Caso o checkout seja de lista de casamento remove os endereços do usuário atual e seta a nova label para o novo endereço
            if(Mage::helper('jbp_giftlist/rules')->checkItemCart() == 'contem'){ 
                unset($options);
                $labelIn = Mage::helper('checkout')->__('Endereço de entrega da lista');
            }
    
            $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'_address_id')
            ->setId($type.'-address-select')
            ->setClass('address-select')
            ->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
            ->setValue($addressId)
            ->setOptions($options);
            $select->addOption('', $labelIn);
    
            return $select->getHtml();
        }
        return '';
    }
    
    public function getAddress()
    {
        $this->setAddressListCasamento();        
        if (is_null($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }    
        return $this->_address;
    }
    
    /*
     * Seta o endereço da lista de casamento referente ao produto que esta no carrinho
     */
    public function setAddressListCasamento(){        
        $address = null;        
        if(Mage::helper('jbp_giftlist/rules')->checkItemCart() == 'contem'){
            $this->_address = Mage::getModel('customer/address')->load(Mage::helper('jbp_giftlist/rules')->getIdAddressListMarriage());
            return $this->_address;
        }
    }
    
}
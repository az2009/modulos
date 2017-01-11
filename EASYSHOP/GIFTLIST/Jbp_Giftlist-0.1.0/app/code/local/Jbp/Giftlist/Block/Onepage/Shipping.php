<?php

class Jbp_Giftlist_Block_Onepage_Shipping extends Mage_Checkout_Block_Onepage_Shipping {
    
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
            
            //Caso o checkout seja de lista de casamento remove os endereços do usuário atual
                if(Mage::helper('jbp_giftlist/rules')->checkItemCart() == 'contem') unset($options);
            
            $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'_address_id')
            ->setId($type.'-address-select')
            ->setClass('address-select')
            ->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
            ->setValue($addressId)
            ->setOptions($options);
    
            //Caso o checkout seja de lista de casamento altera a label
                $labelIn = Mage::helper('checkout')->__('New Address');
                if(Mage::helper('jbp_giftlist/rules')->checkItemCart() == 'contem') $labelIn = Mage::helper('checkout')->__('Endereço de entrega da lista');
            
            $select->addOption('', $labelIn);
    
            return $select->getHtml();
        }
        return '';
    }
    
}

<?php
class Jbp_Giftlist_Model_List extends Mage_Core_Model_Abstract {

    public function _construct(){
        parent::_construct();
        $this->_init('jbp_giftlist/list');
    }

    /**
     * Salva a lista de casamento e executa o upload do banner
     * @param unknown $data
     * @param string $file
     * @throws Exception
     * @return boolean
     */
    public function saveData($data, $file = null){

        try{

            $this->checkAddres($data);

            $address = Mage::getModel('jbp_giftlist/address');
            if($data['jbpgl_id'] && $data['jbpgl_event_address_id']){
                $address->load($data['jbpgl_event_address_id']);
                $this->load($data['jbpgl_id']);
                if(($data['jbpgl_customer_id'] != $this->_getHelper()->getCustomer()->getId()
                    || ($data['jbpgl_event_address_id'] != $this->getData('jbpgl_event_address_id')
                        || (1 != $this->getData('jbpgl_active'))))){

                    throw new Exception($this->_getHelper()->__('Formulário inconsistente'));
                }
            }
            
            if(!$data['jbpgl_id'] && $this->_getHelper()->getList()){                 
                throw new Exception($this->_getHelper()->__('Já existe uma lista ativa por favor feche a lista ativa'));                
            }
            
            $address->addData($data);
            $addressId = $address->save();
            $this->setData('jbpgl_event_address_id', $addressId['jbpgae_id']);
            $this->addData($data);
            $result = $this->save();

            if($result['jbpgl_id'] && !empty($file['jbpgl_banner']['name'])){
                try{
                    $filename = $this->_getHelper()->uploadImage($file['jbpgl_banner'],$result['jbpgl_id']);
                    $this->load($result['jbpgl_id'])->setData('jbpgl_banner', $filename)->save();
                    Mage::getSingleton('core/session')->addSuccess($this->_getHelper()->__('Lista enviada com sucesso'));
                }catch (Exception $e){
                    if(!$data['jbpgl_id']) $this->load($result['jbpgl_id'])->delete();
                    Mage::getSingleton('core/session')->addError($e->getMessage());
                    return;
                }
            }else{
                Mage::getSingleton('core/session')->addSuccess($this->_getHelper()->__('Lista enviada com sucesso'));
            }

        }catch (Exception $e){
            Mage::getSingleton('core/session')->addError($e->getMessage());
            return false;
        }
    }

    /**
     * Fecha a lista de casamento
     */
    public function closedList(){
        $idList = $this->_getHelper()->getList();        
        $rs = Mage::getSingleton('core/resource');
        $modelCoupon = Mage::getModel('jbp_giftlist/coupon_generate');
        $read = $rs->getConnection('core_read');
        $table = $rs->getTableName('jbp_giftlist/list');
        $customerId = $this->_getHelper()->getCustomer()->getId();
        $saldoDisponivel = (double)$this->getSaldoDisponivel();
        $coupon = '';
        $mail = $this->_getHelper()->getCustomer()->getCustomer()->getEmail();        
        
        try{
            //Verifica se a lista possui saldo a resgatar
                if($saldoDisponivel > 0){
                
                    //cria a regra com desconto no carrinho para o saldo restante da lista
                        $rule_id = $modelCoupon->generateRule($saldoDisponivel, $mail, $idList);
                        
                        //verifica se a regra foi criada
                            if($rule_id){
                                
                                //gera o cupom para a regra criada
                                    $result = $modelCoupon->generatorCoupon($rule_id);
                                    
                                    //retorna o código do cupom
                                        $coupon = $result->getFirstItem()->getData('code');                                
                            }
                }
                
                //seta os dados no banco para desativar a lista e gravar o cupom gerado caso haja saldo
                    $read->query("update {$table} set jbpgl_active = '0', jbpgl_coupon_credit = '{$coupon}', jbpgl_balance = '{$saldoDisponivel}'  where jbpgl_customer_id= '{$customerId}' and jbpgl_id = '{$idList}'");
        }catch(Exception $e){            
            //Deleta a regra de desconto caso aconteça algo na hora de dar o update na query, e lança uma exception
                Mage::getModel('salesrule/rule')->load($rule_id)->delete();
                Mage::log($e->getMessage());
                throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Retorna o saldo disponível com base na lista ativa
     * @return number
     */
    public function getSaldoDisponivel(){
        $jbpgl_id = $this->_getHelper()->getList();
        $rs = Mage::getSingleton('core/resource');
        $collection = Mage::getModel('sales/order')
        ->getCollection()
        ->addAttributeToSelect('entity_id')
        ->addFieldToFilter(
            array('status'),
            array(
                array('credito_lista_casamento')
            )
            )
            ->addAttributeToFilter('jbp_giftlist_id', array('eq' => $jbpgl_id))
            ->addAttributeToFilter('jbp_giftlist_resgate', array('eq' => 0));
    
            $collectionSecond = Mage::getModel('sales/order')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('jbp_giftlist_id', array('eq' => $jbpgl_id))
            ->addAttributeToFilter('jbp_giftlist_resgate', array('eq' => 1));
            $collectionSecond->getSelect()->columns('SUM(main_table.base_grand_total) AS total_resgatado');
    
            $collection->getSelect()->columns('SUM(main_table.subtotal) + SUM(main_table.base_shipping_amount) AS qty_total, main_table.total_qty_ordered AS qty_total_qty');
    
            $val = $collection->getFirstItem()->getData('qty_total') - $collectionSecond->getFirstItem()->getData('total_resgatado');
            return $val;    
    }
    

    /**
     * Verifica se o endereço informado pertence ao cliente atual
     * @param unknown $data
     * @throws Exception
     */
    public function checkAddres($data){
        $shippingId = $data['jbpgl_event_shipping_id'];
        if(!$shippingId) throw new Exception($this->_getHelper()->__('Informe o endereço de envio'));
        $customerId = $this->_getHelper()->getCustomer()->getId();
        $customer = Mage::getModel('customer/customer')->load($customerId);
        foreach ($customer->getAddresses() as $address){
            $addressId[] = $address->getId();echo $address->getId();

        }
        if(!in_array($shippingId, $addressId)) throw new Exception($this->_getHelper()->__('Endereço inválido'));
    }

    /**
     * Retorna o objeto da helper da lista de casamento
     */
    public function _getHelper(){
        return Mage::helper('jbp_giftlist');
    }

}
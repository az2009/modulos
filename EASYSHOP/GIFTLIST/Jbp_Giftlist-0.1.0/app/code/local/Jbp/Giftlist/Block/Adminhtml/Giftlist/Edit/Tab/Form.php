<?php

class Jbp_Giftlist_Block_Adminhtml_Giftlist_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
    
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setUseContainer(true);
        $this->setForm($form);
        $fieldset = $form->addFieldset('listmarriage_form', array('legend'=>Mage::helper('jbp_giftlist')->__('Informações')));
        
        
        $fieldset->addField('jbpgl_name_noivo', 'label', array(
            'label'     => Mage::helper('jbp_giftlist')->__('Nome do noivo'),
            'name'      => 'jbpgl_name_noivo',
        ));
        
        $fieldset->addField('jbpgl_name_noiva', 'label', array(
            'label'     => Mage::helper('jbp_giftlist')->__('Nome do noiva'),
            'name'      => 'jbpgl_name_noiva',
        ));
        
        $fieldset->addField('jbpgl_created_at', 'label', array(
            'label'     => Mage::helper('jbp_giftlist')->__('Data de criação'),
            'name'      => 'jbpgl_created_at',
        ));
        
        $fieldset->addField('jbpgl_event_date', 'label', array(
            'label'     => Mage::helper('jbp_giftlist')->__('Data do casamento'),
            'name'      => 'jbpgl_event_date',
        ));
        
        $fieldset->addField('nome', 'label', array(
            'label'     => Mage::helper('jbp_giftlist')->__('Cidade'),
            'name'      => 'nome',
        ));
        
        $fieldset->addField('sigla', 'label', array(
            'label'     => Mage::helper('jbp_giftlist')->__('Estado'),
            'name'      => 'sigla',
        ));
        
        $fieldset->addField('value', 'label', array(
            'label'     => Mage::helper('jbp_giftlist')->__('Endereço de entrega'),
            'name'      => 'value',
        ));
        
        $fieldset->addField('jbpgl_description', 'label', array(
            'label'     => Mage::helper('jbp_giftlist')->__('Mensagem do casal'),
            'name'      => 'jbpgl_description',
        ));
        
        $fieldset->addField('produtos_recebidos', 'label', array(
            'label'     => Mage::helper('jbp_giftlist')->__('Produto(s) recebido(s)'),
            'name'      => 'produtos_recebidos',
            'after_element_html'=> $this->getValuesOfListMarriageReceived()['total'].' = '.$this->getValuesOfListMarriageReceived()['qty'].' produto(s) recebido(s)'
        ));
        
        $fieldset->addField('saldo_resgatado', 'label', array(
            'label'     => Mage::helper('jbp_giftlist')->__('Saldo resgatado'),
            'name'      => 'saldo_resgatado',
            'after_element_html'=> $this->getValuesOfListMarriageRansomed()['total'].' = '.$this->getValuesOfListMarriageRansomed()['qty'].' produto(s) resgatado(s)'
        ));
        
        $fieldset->addField('saldo_disponivel', 'label', array(
            'label'     => Mage::helper('jbp_giftlist')->__('Saldo disponível'),
            'name'      => 'saldo_disponivel',
            'after_element_html'=> $this->getSaldoDisponivel()
        ));
        
        $fieldset->addField('jbpgl_coupon_credit_is', 'label', array(
            'label'     => Mage::helper('jbp_giftlist')->__('Cupom de crédito'),
            'name'      => 'jbpgl_coupon_credit_is',
            'after_element_html'  => ($this->getAttrList()->getData('jbpgl_coupon_credit')) ? (!Mage::helper('jbp_giftlist')->isValidCoupon($this->getAttrList()->getData('jbpgl_coupon_credit'))) ? $this->getAttrList()->getData('jbpgl_coupon_credit').' - <strong style="color:green">disponível</strong>' : $this->getAttrList()->getData('jbpgl_coupon_credit').' - <strong style="color:red">indisponível</strong>' : '----'
        ));
        
        $fieldset->addField('jbpgl_active_is', 'label', array(
            'label'     => Mage::helper('jbp_giftlist')->__('Status da lista'),
            'name'      => 'jbpgl_active_is',
            'after_element_html'  => ($this->getAttrList()->getData('jbpgl_active')) ? '<strong style="color:blue">Ativado</strong>' : '<strong style="color:gray">Desativado</strong>'
        ));
        
        $form->setValues($this->getCollection());
        return parent::_prepareForm();
    }
    
    
    public function getAttrList(){
        $list = Mage::getModel('jbp_giftlist/list')->load($this->getListId());
        return $list;
    }
    
    public function getListId(){
        return Mage::registry('list_marriage');
    }
    

    public function getCollection(){
        $idList = $this->getListId();
        $resource = Mage::getSingleton('core/resource');
        $collection = Mage::getModel('jbp_giftlist/list')
                        ->getCollection()
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('jbpgl_id', array('eq'=>$idList));
        $collection
        ->getSelect()
        ->join(
            array('address' => $resource->getTableName('jbp_giftlist/address')),
            'address.jbpgae_id = main_table.jbpgl_event_address_id'
        )
        ->join(
            array('cidade' => $resource->getTableName('cidades')),
            'cidade.cod_cidades = address.jbpgae_cidade'
        )
        ->join(
            array('uf' => $resource->getTableName('estados')),
            'uf.cod_estados = address.jbpgae_estado'
        )
        ->join(
            array('shipping' => $resource->getTableName('customer_address_entity_text')),
            'shipping.entity_id = main_table.jbpgl_event_shipping_id'
        );
                
        $this->getValuesOfListMarriage();
        
        return $collection->getFirstItem()->getData();
    }
    
    public function getValuesOfListMarriageRansomed(){
        
        $jbpgl_id = $jbpgl_id = $this->getListId();
        
        $rs = Mage::getSingleton('core/resource');
        
        $collection = Mage::getModel('sales/order')
        ->getCollection()
        ->addAttributeToSelect('*')
        ->addAttributeToFilter('jbp_giftlist_resgate',array('eq' => 1))
        ->addAttributeToFilter('jbp_giftlist_id', array('eq' => $jbpgl_id));
        
        $collection->getSelect()->columns('SUM(main_table.base_grand_total) AS qty_total, SUM(main_table.total_qty_ordered) AS qty_total_qty');
        
        if($collection->getFirstItem()->getData('qty_total') > 0) $total = $collection->getFirstItem()->getData('qty_total');
        
        $values = array(
            'total' => Mage::helper('core')->currency($total,true,false),
            'qty' => (int)$collection->getFirstItem()->getData('qty_total_qty')
        );
        
        return $values;
        
    }
    
    public function getValuesOfListMarriageReceived(){
        $jbpgl_id = $this->getListId();        
        $status = array('credito_lista_casamento');
        $values = array();
        $rs = Mage::getSingleton('core/resource');        
        $collection = Mage::getModel('sales/order')
        ->getCollection()
        ->addAttributeToSelect('*')
        ->addFieldToFilter(
            array('status'),
            array(
                array('in' => $status),
            )
        )
        ->addAttributeToFilter('jbp_giftlist_id', array('eq' => $jbpgl_id));
    
        $collection->getSelect()->columns('SUM(main_table.subtotal) + SUM(main_table.base_shipping_amount) AS qty_total, SUM(main_table.total_qty_ordered) AS qty_total_qty');
    
        if($collection->getFirstItem()->getData('qty_total') > 0) $total = $collection->getFirstItem()->getData('qty_total');
        
        $values = array(
            'total' => Mage::helper('core')->currency($total,true,false),
            'qty' => (int)$collection->getFirstItem()->getData('qty_total_qty')
        );            
        
        return $values;
    }
    
    public function getSaldoDisponivel(){
        $jbpgl_id = $this->getListId();
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
            
            $coupon = $this->getAttrList()->getData('jbpgl_coupon_credit');
            
            if(Mage::helper('jbp_giftlist')->isValidCoupon($coupon)){
                $valdiscount = '<span style="color:red"> -'.Mage::helper('core')->currency($this->getAttrList()->getData('jbpgl_balance'),true,false).'</span>';
            }
            
            return Mage::helper('core')->currency($val,true,false).$valdiscount.'';
            
    }

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}

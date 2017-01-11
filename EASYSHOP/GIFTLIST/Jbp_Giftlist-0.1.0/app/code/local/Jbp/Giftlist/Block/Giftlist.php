<?php
class Jbp_Giftlist_Block_Giftlist extends Mage_Core_Block_Template {

    public $totalValue = 0;
    public $totalQty   = 0;
    public $totalValueRansomed = 0;
    public $totalQtyRansomed = 0;

    public function __construct(){

        $this->getTotalOfProductsReceived();
        $this->getTotalOfProductsRansomed();
        parent::__construct();
    }

    public function getCustomer(){
        return Mage::getSingleton('customer/session');
    }

    public function getBlockList(){
        return $this->getLayout()->createBlock('jbp_giftlist/product_list');
    }

    public function getShippingId(){
        $customer = Mage::getModel('customer/customer')->load($this->getCustomer()->getId())->getAddresses();
        return $customer; 
    }

    public function getListActive(){

        if(!$this->_getHelper()->getAction()){
            return;
        }
        $collection = Mage::getModel('jbp_giftlist/list')
                    ->getCollection()
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('main_table.jbpgl_active',1)
                    ->addFieldToFilter('main_table.jbpgl_customer_id', $this->_getHelper()->getCustomer()->getId());

        $collection->getSelect()
                   ->join('jbpgiftlist_address_event', 'jbpgiftlist_address_event.jbpgae_id = main_table.jbpgl_event_address_id', array('*'));

        $r = $collection->getFirstItem()->getData();;

        if(count($collection->getData())){
            return $r;
        }
        return;
    }

    public function getEstados(){
        $con = Mage::getSingleton('core/resource');
        $read = $con->getConnection('core_read');
        $sql = $read->select()->from($con->getTableName('estados'))->order('sigla');
        return $read->fetchAll($sql);
    }

    public function getListSearch(){

        $cidade = $this->getData('jbpgae_cidade');
        $estado = $this->getData('jbpgae_estado');
        $data = $this->getData('jbpgl_event_date');
        $nomeNoivoNoiva = $this->getData('jbpgl_name_noivo_ambos');

        if(empty($cidade) && empty($estado) && empty($data) && empty($nomeNoivoNoiva)){ return;}

        $collection = Mage::getModel('jbp_giftlist/list')
                        ->getCollection()
                        ->addFieldToFilter('jbpgl_active',array('eq'=>1))
                        ->addFieldToSelect('*');

        if(!empty($data)){
            $collection->addFieldToFilter('jbpgl_event_date', $data);
        }

        if(!empty($nomeNoivoNoiva)){
            $collection->addFieldToFilter(
                array('jbpgl_name_noivo','jbpgl_name_noiva'),
                array(
                    array('like' => "%{$nomeNoivoNoiva}%"),
                    array('like' => "%{$nomeNoivoNoiva}%"),
                ));
        }

        $collection->getSelect()->join(
            'jbpgiftlist_address_event',
            'main_table.jbpgl_event_address_id = jbpgiftlist_address_event.jbpgae_id',
             array('*'));

        if(!empty($cidade)){
            $collection->getSelect()->where('jbpgiftlist_address_event.jbpgae_cidade=?', $cidade);
        }

        if(!empty($estado)){
            $collection->getSelect()->where('jbpgiftlist_address_event.jbpgae_estado=?', $estado);
        }

        return $collection->getData();
    }


    /**
     * Retorna os ids dos produtos recebidos ou resgatados para o  handle listacasamento_index_listacasamentoprodutos
     * @return unknown
     */
    public function getAllIdOfProductsReceivedPublic($status = array('processing','complete','credito_lista_casamento','resgate_lista_casamento','pending')){
        $total = 0;
        $products = array();
        $list = Mage::getModel('jbp_giftlist/list')->load($this->getRequest()->getParam('id'));
        if(count($list->getData())){
            $jbpgl_id = $list->getData('jbpgl_id');

            $rs = Mage::getSingleton('core/resource');

            $collection = Mage::getModel('sales/order')
            ->getCollection()
            ->addFieldToSelect ('entity_id','jbp_giftlist_id','status')
            ->addFieldToFilter(
                array('status'),
                array(
                    array('in' => $status),
                )
            )
            ->addAttributeToFilter('jbp_giftlist_id', array('eq' => $jbpgl_id));


            $collection->getSelect()
            ->join(
                array('soi' => $rs->getTableName('sales/order_item')),
                'soi.order_id = main_table.entity_id',
                array('product_id')
            );

            foreach($collection as $item){
                $products[] = $item->getData('product_id');
            }
            return $products;

        }

    }

    /**
     * Retorna os ids dos produtos recebidos ou resgatados para o handle catalog_product_view
     * @return unknown
     */
    public function getAllIdOfProductsReceivedPublicView($status = array('processing','complete','credito_lista_casamento','resgate_lista_casamento')){
        $total = 0;
        $products = array();
        $list = Mage::getModel('jbp_giftlist/list')->load($this->getRequest()->getParam('listcasamento'));
        if(count($list->getData())){
            $jbpgl_id = $list->getData('jbpgl_id');

            $rs = Mage::getSingleton('core/resource');

            $collection = Mage::getModel('sales/order')
            ->getCollection()
            ->addFieldToSelect ('entity_id','jbp_giftlist_id','status')
            ->addFieldToFilter(
                array('status'),
                array(
                    array('in' => $status),
                )
            )
            ->addAttributeToFilter('jbp_giftlist_id', array('eq' => $jbpgl_id));


            $collection->getSelect()
            ->join(
                array('soi' => $rs->getTableName('sales/order_item')),
                'soi.order_id = main_table.entity_id',
                array('product_id')
            );

            foreach($collection as $item){
                $products[] = $item->getData('product_id');
            }
            return $products;

        }

    }

    /**
     * Retorna o objeto da lista de casamento do usuario logado
     */
    public function getListByCustomer(){
        return Mage::getModel('jbp_giftlist/list')->load($this->_getHelper()->getList());
    }

    /**
     * Retorna o valor e quantidade dos produtos recebidos
     * @return unknown
     */
    public function getTotalOfProductsReceived($status = array('credito_lista_casamento')){
        $total = 0;
        $products = array();
        $list = $this->_getHelper()->getList();
        if(count($list)){

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
            ->addAttributeToFilter('jbp_giftlist_id', array('eq' => $list));

            $collection->getSelect()->columns('SUM(main_table.subtotal) + SUM(main_table.base_shipping_amount) AS qty_total, SUM(main_table.total_qty_ordered) AS qty_total_qty');

            if($collection->getFirstItem()->getData('qty_total') > 0) $total = $collection->getFirstItem()->getData('qty_total');

            $this->totalValue = Mage::helper('core')->currency($total,true,false);
            $this->totalQty   = (int)$collection->getFirstItem()->getData('qty_total_qty');
        }
    }


    /**
     * Retorna o valor e quantidade dos produtos resgatados
     * @return unknown
     */
    public function getTotalOfProductsRansomed($status = array('resgate_lista_casamento')){
        $total = 0;
        $products = array();
        $list = $this->_getHelper()->getList();
        if(count($list)){

            $rs = Mage::getSingleton('core/resource');

            $collection = Mage::getModel('sales/order')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('jbp_giftlist_resgate',array('eq' => 1))
            ->addAttributeToFilter('jbp_giftlist_id', array('eq' => $list));

            $collection->getSelect()->columns('SUM(main_table.base_grand_total) AS qty_total, SUM(main_table.total_qty_ordered) AS qty_total_qty');

            if($collection->getFirstItem()->getData('qty_total') > 0) $total = $collection->getFirstItem()->getData('qty_total');

            $this->totalValueRansomed = $total;
            $this->totalQtyRansomed   = (int)$collection->getFirstItem()->getData('qty_total_qty');

        }

    }

    /**
     * Retorna os ids dos produtos recebidos ou resgatados
     * @return unknown
     */
    public function getAllIdOfProductsReceived($status = array('processing','complete','credito_lista_casamento','resgate_lista_casamento')){
        $total = 0;
        $products = array();
        $list = $this->_getHelper()->getList();
        if(count($list)){

            $rs = Mage::getSingleton('core/resource');

            $collection = Mage::getModel('sales/order')
                            ->getCollection()
                            ->addFieldToSelect ('entity_id','jbp_giftlist_id','status')
                            ->addFieldToFilter(
                                array('status'),
                                array(
                                    array('in' => $status),
                                )
                            )
                            ->addAttributeToFilter('jbp_giftlist_id', array('eq' => $list));


            $collection->getSelect()
            ->join(
                array('soi' => $rs->getTableName('sales/order_item')),
                'soi.order_id = main_table.entity_id',
                array('product_id','order_id')
            );

            foreach($collection as $item){
                $products[] = $item->getData('product_id');
            }
            return $products;

        }

    }

    /**
     * Retorna o saldo disponivel para resgate de produtos
     * Soma o total de produtos a serem resgatados
     */
    public function getSaldoDisponivelParaResgate($config = null){
        $item = null;
        $products = array();
        $list = $this->_getHelper()->getList();
        if(count($list)){
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
            ->addAttributeToFilter('jbp_giftlist_id', array('eq' => $list))
            ->addAttributeToFilter('jbp_giftlist_resgate', array('eq' => 0));

            $collectionSecond = Mage::getModel('sales/order')
                                    ->getCollection()
                                    ->addAttributeToSelect('*')
                                    ->addAttributeToFilter('jbp_giftlist_id', array('eq' => $list))
                                    ->addAttributeToFilter('jbp_giftlist_resgate', array('eq' => 1));
            $collectionSecond->getSelect()->columns('SUM(main_table.base_grand_total) AS total_resgatado');

            $collection->getSelect()->columns('SUM(main_table.subtotal) + SUM(main_table.base_shipping_amount) AS qty_total, main_table.total_qty_ordered AS qty_total_qty');

            $val = $collection->getFirstItem()->getData('qty_total') - $collectionSecond->getFirstItem()->getData('total_resgatado');

            return $val;

        }
        return;
    }

    /**
     * Retorna as cidades de acordo com o código informado
     * @param unknown $code
     * @return unknown
     */
    public function getCidadeByCode($code){
        $con = Mage::getSingleton('core/resource');
        $read = $con->getConnection('core_read');
        $sql = $read->select()
                    ->from($con->getTableName('cidades'))
                    ->where('cod_cidades=?', $code)
                    ->order('nome');
        $data = $read->fetchAll($sql);
        return $data[0]['nome'];
    }

    /**
     * Retorna os estados de acordo com o código informado
     * @param unknown $code
     * @return unknown
     */
    public function getEstadosByCode($code){
        $con = Mage::getSingleton('core/resource');
        $read = $con->getConnection('core_read');
        $sql = $read->select()
        ->from($con->getTableName('estados'))
        ->where('cod_estados=?', $code)
        ->order('cod_estados');
        $data = $read->fetchAll($sql);
        return $data[0]['sigla'];
    }

    public function _getHelper(){
        return Mage::helper('jbp_giftlist');
    }





















}
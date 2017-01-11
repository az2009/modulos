<?php
class Jbp_Giftlist_Model_Item extends Mage_Core_Model_Abstract {

    public function _construct(){
        parent::_construct();
        $this->_init('jbp_giftlist/item');
    }
    
    /**
     * Remove o item informado da lista de casamento 
     * @param unknown $id
     * @throws Exception
     * @return boolean
     */
    public function removeData($id){

        try{

            if(!$this->_getHelper()->getCustomer()->isLoggedIn()){
                throw new Exception(Mage::helper('core')->__('Efetue seu login para adicionar o produto a sua lista Clique %s', '<a href="'.Mage::getUrl('listaCasamento/index/').'">Aqui</a>'));
            }

            if(!$this->_getHelper()->getList()){
                throw new Exception(Mage::helper('core')->__('Você ainda não tem uma lista criada, crie primeiro a sua lista Clique %s', '<a href="'.Mage::getUrl('listaCasamento/index/').'">Aqui</a>'));
            }

            if(in_array($id, $this->getAllIdOfProductsReceived())){
                throw new Exception(Mage::helper('core')->__('Este item não pode ser deletado da lista pois ele já foi processado.'));
            }

            $collection = $this->getCollection()
                               ->addFieldToFilter('jbpgi_product_id', $id)
                               ->addFieldToFilter('jbpgi_list_id', $this->_getHelper()->getList())
                               ->getFirstItem()
                               ->getData();

            if(!count($collection)){
                throw new Exception(Mage::helper('core')->__('Você não possui produtos a serem removidos'));
            }

            $this->load($collection['jbpgi_id'])->delete();
            Mage::getSingleton('core/session')->addSuccess('Produto removido com sucesso');
        }catch(Exception $e){
            Mage::getSingleton('core/session')->addError($e->getMessage());
            Mage::log($e->getMessage());
            return false;
        }
    }

    /**
     * Seta no item o valor caso o mesmo for resgatado e dispara o e-mail para o usuário da lista
     * @param unknown $id
     * @param unknown $listId
     */
    public function saveRansomed($id, $listId){
        try{

            $item = Mage::getSingleton('jbp_giftlist/item')->load($this->getIdOfProductByIdListAndProducId($id, $listId));
            $item->setData('jbpgi_resgatar', 1)->save();

            $data = array(
                'template_id' => 'jbp_giftlist_email_template_notify_ransomed',
                'subject' => $this->_getHelper()->__('Presente resgatado'),
                'mail_destinatario' => $this->_getHelper()->getCustomer()->getCustomer()->getEmail(),
                'log_error' => 'Não foi possível enviar o e-mail de notificação de produtos',
                'nameProduct' => Mage::getModel('catalog/product')->load($id)->getName()
            );

            $this->_getHelper()->sendEmailGeneral($data);

            Mage::getSingleton('core/session')->addSuccess($this->_getHelper()->__('Produto resgatado com sucesso'));

        }catch(Exception $e){
            Mage::getSingleton('core/session')->addError($this->_getHelper()->__('Ocorreu um erro tente novamente'));
            Mage::log('Não foi possível resgatar o produto: '.$id);
        }
    }

    /**
     * Retorna o id do produto baseado na lista e id do produto
     * @param unknown $id
     * @param unknown $listId
     */
    public function getIdOfProductByIdListAndProducId($id, $listId){
        $collection  = Mage::getModel('jbp_giftlist/item')
                        ->getCollection()
                        ->addFieldToFilter('jbpgi_list_id',$listId)
                        ->addFieldToFilter('jbpgi_product_id',$id);
        return $collection->getFirstItem()->getData('jbpgi_id');
    }

    /**
     * Adiciona o produto a lista de casamento
     * @param unknown $id
     * @throws Exception
     * @return boolean
     */
    public function saveData($id){
        try{

           if(!$this->_getHelper()->getCustomer()->isLoggedIn()){
               throw new Exception(Mage::helper('core')->__('Efetue seu login para adicionar o produto a sua lista Clique %s', '<a href="'.Mage::getUrl('listaCasamento/index/').'">Aqui</a>'));
           }

           if(!$this->_getHelper()->getList()){
               throw new Exception(Mage::helper('core')->__('Você ainda não tem uma lista criada, crie primeiro a sua lista Clique %s', '<a href="'.Mage::getUrl('listaCasamento/index/').'">Aqui</a>'));
           }

           if(count($this->_getHelper()->checkItemExists($id))){
               throw new Exception('Produto já adicionado a sua lista');
           }
           $this->setData('jbpgi_product_id', $id);
           $this->setData('jbpgi_list_id', $this->_getHelper()->getList());
           $this->save();
           Mage::getSingleton('core/session')->addSuccess('Produto adicionado');
        }catch (Exception $e){
            Mage::getSingleton('core/session')->addError($e->getMessage());
            Mage::log($e->getMessage());
            return false;
        }
    }

    /**
     * Retorna os ids dos produtos recebidos ou resgatados
     * @return unknown
     */
    public function getAllIdOfProductsReceived($status = array('processing','complete','credito_lista_casamento','resgate_lista_casamento')){
        $total = 0;
        $products = array();
        $list = Mage::getModel('jbp_giftlist/list')->load($this->_getHelper()->getCustomer()->getId(), 'jbpgl_customer_id');
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
                array('product_id','order_id')
            );

            foreach($collection as $item){
                $products[] = $item->getData('product_id');
            }
            return array_unique($products);
        }

    }
    
    /**
     * Retorna o objeto da helper
     */
    public function _getHelper(){
        return Mage::helper('jbp_giftlist');
    }

}
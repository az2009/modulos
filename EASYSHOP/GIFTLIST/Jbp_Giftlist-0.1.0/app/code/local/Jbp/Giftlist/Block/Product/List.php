<?php
class Jbp_Giftlist_Block_Product_List extends Mage_Catalog_Block_Product_List {



    public function setProductCollection(){
        $storeId    = Mage::app()->getStore()->getId();
        $collection = Mage::getResourceModel('catalog/product_collection')
                        ->addUrlRewrite()
                        ->addAttributeToSelect(array('name', 'price', 'thumbnail', 'short_description','image','small_image','url_key'), 'inner')
                        ->addAttributeToFilter('status','1')
                        ->setStoreId($storeId)
                        ->addStoreFilter($storeId);

            //Lista todos os produtos ganhos
        if($this->getRequest()->getParam('item') == 'produtosGanhos'){

            $collection->addAttributeToFilter('entity_id',array('in'=>$this->getBlockGiftList()->getAllIdOfProductsReceived(array('credito_lista_casamento'))));

            //Lista todos os produtos que o mesmo recebeu resgatados
        }elseif($this->getRequest()->getParam('item') == 'resgatarProdutos'){

            $collection->addAttributeToFilter('entity_id',array('in' => $this->getItemByids()));

            //Lista todos os produtos resgatados
        }elseif($this->getRequest()->getParam('item') == 'produtosResgatados'){

            $collection->addAttributeToFilter('entity_id',array('in'=>$this->getBlockGiftList()->getAllIdOfProductsReceived(array('resgate_lista_casamento','complete'))));

            //Lista todos os produtos que o mesmo deseja ganhar
        }else{
            $collection->addAttributeToFilter('entity_id',array('in' => $this->getItemByids()));

        }

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        return $collection;
    }

    /**
     * Pega os produtos resgatados
     * @return unknown
     */
    public function getProductsRansomed(){
        $list = $this->_getHelper()->getList();
        $storeId    = Mage::app()->getStore()->getId();
        $rs = Mage::getSingleton('core/resource');
        $products = array();
        $collection = Mage::getResourceModel('catalog/product_collection')
        ->addUrlRewrite()
        ->addAttributeToSelect('entity_id')
        ->addAttributeToFilter('status','1')
        ->setStoreId($storeId)
        ->addStoreFilter($storeId);

        $collection->getSelect()->join(
                array('jgi' => $rs->getTableName('jbp_giftlist/item')),
                "(jgi.jbpgi_product_id = entity_id) AND (jgi.jbpgi_resgatar = 1) AND (jgi.jbpgi_list_id = {$list})",
                array('*')
            );

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        foreach($collection as $item){
            $products[] = $item->getData('entity_id');
        }

        return $products;

    }

    public function getColumnCount(){
        return 4;
    }


    //Retorna os itens da lista de casamento do usuário logado
    public function getItemByids(){
        $productids = null;
        foreach($this->_getHelper()->checkItemTotal() as $item){
            $productids[] = $item['jbpgi_product_id'];
        }
        return $productids;
    }

    public function _getHelper(){
        return Mage::helper('jbp_giftlist');
    }

    //Pega todos os produtos da lista de casamento que não foram comprados ou resgatados
    public function _setProductCollection(){

        $storeId    = Mage::app()->getStore()->getId();
        $this->_productCollection = Mage::getResourceModel('catalog/product_collection')
                                        ->addUrlRewrite()
                                        ->addAttributeToSelect('*')
                                        ->addAttributeToFilter('status','1')
                                        ->addAttributeToFilter('entity_id', array('in'=>$this->_getHelper()->getItemsOfListById((int)$this->getRequest()->getParam('id'))))
                                        ->setStoreId($storeId)
                                        ->addStoreFilter($storeId);

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_productCollection);

        return $this->_productCollection;
    }

    public function getBlockGiftList(){
        return $this->getLayout()->createBlock('jbp_giftlist/giftlist');
    }

    public function getList(){
        $id = $this->getRequest()->getParam('id');
        return Mage::getModel('jbp_giftlist/list')->load($id);
    }

    public function getBanner(){
        $imgList = $this->getList()->getData('jbpgl_banner');

        if(!empty($imgList)){
            return $this->_getHelper()->getPathImage($imgList);
        }
        return $this->_getHelper()->getPathImage('img_default.jpg');
    }

    public function getLoadedProductCollection()
    {
               $this->_setProductCollection();
        return $this->_getProductCollection();
    }

}
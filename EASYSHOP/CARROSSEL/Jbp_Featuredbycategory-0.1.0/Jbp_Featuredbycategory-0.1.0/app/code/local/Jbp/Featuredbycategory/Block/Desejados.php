<?php
class Jbp_Featuredbycategory_Block_Desejados extends Mage_Catalog_Block_Product_Abstract{

    public function _construct(){
        $this->setTemplate('jbp/jbp_bestseller.phtml');
    }

    public function getBestSellerProductCollection(){
        return $this->getWhiteListProductCollection();
    }


    public function getWhiteListProductCollection(){

        if(!$this->getData('is_enabled')) return;

        $storeId    = Mage::app()->getStore()->getId();

        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $categoryId    = $this->getData('category_id');

        $size = $this->getData('size');

        if($categoryId){

            $collection = Mage::getResourceModel('catalog/product_collection')
                                ->addUrlRewrite()
                                ->addAttributeToSelect(array('name', 'price', 'thumbnail', 'short_description','image','small_image','url_key'), 'inner')
                                ->addAttributeToFilter('status','1')
                                ->setStoreId($storeId)
                                ->addStoreFilter($storeId);

            if(($categoryId != 'all') && !empty($categoryId)){
                $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                           ->addAttributeToFilter(array(array('attribute' => 'category_id', array('in' => array('finset' => $categoryId)))));
            }

            $collection->getSelect()
                       ->where('entity_id in ('.$this->getProductIdWhiteList().')', array());

            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

            return $collection;

        }
        return;
    }



    public function getProductIdWhiteList(){
        $con    = Mage::getSingleton('core/resource');
        $read   = $con->getConnection('core_read');
        $sql    = "select product_id from wishlist_item group by product_id order by count(product_id) desc, product_id desc limit 0, 20";
        $result = $read->fetchAll($sql);
        return $this->prepareFilter($result);
    }

    public function prepareFilter($result){
        if(is_array($result)){
            foreach ($result as $item){
                $data[] = $item['product_id'];
            }
            return implode(',', $data);
        }
        return;
    }


}
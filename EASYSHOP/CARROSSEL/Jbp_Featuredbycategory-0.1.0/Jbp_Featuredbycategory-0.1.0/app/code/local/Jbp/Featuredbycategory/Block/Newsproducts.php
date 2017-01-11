<?php
class Jbp_Featuredbycategory_Block_Newsproducts extends Mage_Catalog_Block_Product_Abstract{

    public function _construct(){
        #$this->setTemplate('jbp/jbp_newsproducts.phtml');
        $this->setTemplate('jbp/jbp_bestseller.phtml');
    }

    public function getBestSellerProductCollection(){
        return $this->getNewProductCollection();
    }

    public function getNewProductCollection(){

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
                            ->addStoreFilter($storeId)
                            ->setPageSize((int)$size)
                            ->addAttributeToFilter('news_from_date', array('date' => true, 'to' => $todayDate))
                            ->addAttributeToFilter('news_to_date', array('or'=> array(
                                0 => array('date' => true, 'from' => $todayDate),
                                1 => array('is' => new Zend_Db_Expr('null')))
                            ), 'left')
                            ->addAttributeToSort('news_from_date', 'desc');

            if(($categoryId != 'all') && !empty($categoryId)){
                $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                           ->addAttributeToFilter(array(array('attribute' => 'category_id', array('in' => array('finset' => $categoryId)))));
            }

            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

            return $collection;
        }

        return;

    }

}
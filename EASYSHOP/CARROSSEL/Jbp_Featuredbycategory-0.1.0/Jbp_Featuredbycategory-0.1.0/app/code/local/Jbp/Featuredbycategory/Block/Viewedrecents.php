<?php
class Jbp_Featuredbycategory_Block_Viewedrecents extends Mage_Reports_Block_Product_Viewed{

    public function _construct(){
        $this->setTemplate('jbp/jbp_bestseller.phtml');
    }

    public function getBestSellerProductCollection(){
        return $this->getViewedRecentsProductCollection();
    }

    public function getViewedRecentsProductCollection(){

        if(!$this->getData('is_enabled')) return;

        $storeId    = Mage::app()->getStore()->getId();
        $categoryId    = $this->getData('category_id');
        $size = $this->getData('size');

        if($categoryId){

            $collection = $this->getRecentlyViewedProducts();

            $collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                        ->setStoreId($storeId)
                        ->addStoreFilter($storeId)
                        ->setPageSize($size);

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
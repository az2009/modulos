<?php
class Jbp_Featuredbycategory_Block_Mostclick extends Mage_Catalog_Block_Product_Abstract{

    public function _construct(){
        $this->setTemplate('jbp/jbp_bestseller.phtml');
    }

    public function getBestSellerProductCollection(){
        return $this->getMostViewedProductCollection();
    }

    public function getMostViewedProductCollection(){

        if(!$this->getData('is_enabled')) return;

        $storeId    = Mage::app()->getStore()->getId();
        $categoryId    = $this->getData('category_id');
        $size = $this->getData('size');

        if($categoryId){

            $collection = Mage::getResourceModel('reports/product_collection')
                                ->addOrderedQty()
                                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                                ->setStoreId($storeId)
                                ->addStoreFilter($storeId)
                                ->addViewsCount()
                                ->setOrder('views_count', 'desc')
                                ->setPageSize($size);

            $productFlatData = Mage::getStoreConfig('catalog/frontend/flat_catalog_product');

            if($productFlatData == "1")
            {
                $collection->getSelect()->joinLeft(
                        array('flat' => 'catalog_product_flat_'.$storeId),
                        "(e.entity_id = flat.entity_id ) ",
                        array(
                            'flat.name AS name','flat.small_image AS small_image','flat.price AS price','flat.special_price as special_price','flat.special_from_date AS special_from_date','flat.special_to_date AS special_to_date'
                        )
                    );
            }

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
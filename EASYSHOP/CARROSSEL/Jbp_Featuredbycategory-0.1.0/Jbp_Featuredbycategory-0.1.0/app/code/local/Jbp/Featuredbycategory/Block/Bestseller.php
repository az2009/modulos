<?php
class Jbp_Featuredbycategory_Block_Bestseller extends Mage_Catalog_Block_Product_Abstract{

    public function _construct(){
        $this->setTemplate('jbp/jbp_bestseller.phtml');
    }
    
    public function getBestSellerProductCollection(){
        
        if(!$this->getData('is_enabled')) return;
        $storeId    = Mage::app()->getStore()->getId();
        $categoryId    = $this->getData('category_id');
        $size = $this->getData('size');
		
        $toDate 	= date('y-m-d');
        $fromDate 	= date('y-m-1');
        
        $collection = Mage::getResourceModel('catalog/product_collection')
                            ->addAttributeToSelect('*')
                            ->addStoreFilter()
                            ->addPriceData()
                            ->addTaxPercents()
                            ->addUrlRewrite()
                            ->setPageSize($size);
        
        $collection->getSelect()
                   ->joinLeft(
                        array('aggregation' => $collection->getResource()->getTable('sales/bestsellers_aggregated_monthly')),
                        "e.entity_id = aggregation.product_id AND aggregation.store_id={$storeId} AND aggregation.period BETWEEN '{$fromDate}' AND '{$toDate}'",
                        array('SUM(aggregation.qty_ordered) AS sold_quantity')
                        )
                        ->group('e.entity_id')
                        ->order(array('sold_quantity DESC', 'e.created_at'));
                
		if(($categoryId != 'all') && !empty($categoryId)){
		    $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
		               ->addAttributeToFilter(array(array('attribute' => 'category_id', array('in' => array('finset' => $categoryId)))));
		}
		
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        
		return $collection; 				
    }
    
    
}
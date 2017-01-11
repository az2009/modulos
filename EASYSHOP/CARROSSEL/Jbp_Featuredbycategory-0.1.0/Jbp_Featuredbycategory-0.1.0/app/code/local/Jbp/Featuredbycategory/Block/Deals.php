<?php
class Jbp_Featuredbycategory_Block_Deals extends Mage_Catalog_Block_Product_Abstract{

    public function _construct(){
        $this->setTemplate('jbp/jbp_bestseller.phtml');
    }

    public function getBestSellerProductCollection(){
        return $this->getAllProductsPromotion();
    }

    public function getAllProductsPromotion(){

        if(!$this->getData('is_enabled')) return;

        $storeId    = Mage::app()->getStore()->getId();
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $categoryId = $this->getData('category_id');
        $size       = $this->getData('size');

        if($categoryId){

            $todayStartOfDayDate  = Mage::app()->getLocale()->date()
                                               ->setTime('00:00:00')
                                               ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

            $todayEndOfDayDate  = Mage::app()->getLocale()->date()
                                             ->setTime('23:59:59')
                                             ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

            $collection = Mage::getSingleton('catalog/product')
                            ->getCollection()
                            ->addAttributeToSelect('*')
                            ->addAttributeToFilter(
                                'special_price', array('notnull' => true)
                                )
                            ->addAttributeToFilter('special_from_date', array('or'=> array(
                                    0 => array('date' => true, 'to' => $todayEndOfDayDate),
                                    1 => array('is' => new Zend_Db_Expr('null')))
                                ), 'left')
                            ->addAttributeToFilter('special_to_date', array('or'=> array(
                                    0 => array('date' => true, 'from' => $todayStartOfDayDate),
                                    1 => array('is' => new Zend_Db_Expr('null')))
                                ), 'left')
                            ->addAttributeToFilter(
                                        array(
                                            array('attribute' => 'special_from_date', 'is'=>new Zend_Db_Expr('not null')),
                                            array('attribute' => 'special_to_date', 'is'=>new Zend_Db_Expr('not null'))
                                        )
                                    )
                            ->addAttributeToSort('special_from_date', 'desc')
                            ->setStoreId($storeId)
                            ->setPageSize($size);

            if(($categoryId != 'all') && !empty($categoryId)){
                $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                           ->addAttributeToFilter(array(array('attribute' => 'category_id', array('in' => array('finset' => $categoryId)))));
            }

            $collection->getSelect()->order('RAND()');

            return $collection;
        }

        return;

    }

}
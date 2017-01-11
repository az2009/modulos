<?php
class Jbp_Featuredbycategory_Block_Indicamos extends Mage_Catalog_Block_Product_Abstract{

    public function _construct(){
        $this->setTemplate('jbp/jbp_bestseller.phtml');
    }

    public function getBestSellerProductCollection(){
        return $this->getIndicamosProductCollection();
    }

    public function getIndicamosProductCollection(){

        if(!$this->getData('is_enabled')) return;

        $storeId    = Mage::app()->getStore()->getId();
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $categoryId    = $this->getData('category_id');
        $size = $this->getData('size');

        $atr = explode(',', $this->_getHelper()->getAttributeProductsRandom());

        if($this->getProduct() && is_array($atr)){

            foreach ($atr as $attribute){

                $collection = Mage::getResourceModel('catalog/product_collection')
                                    ->addUrlRewrite()
                                    ->addAttributeToSelect(array('sku','name', 'price', 'thumbnail', 'short_description','image','small_image','url_key'), 'inner')
                                    ->addAttributeToFilter('status','1')
                                    ->addAttributeToFilter(
                                            array(
                                                array('attribute' => $attribute, 'eq' => $this->getProduct()->getData($attribute))
                                            )
                                        )
                                    ->addAttributeToFilter(
                                        array(
                                            array('attribute' => $attribute, 'neq' => null)
                                        )
                                    )
                                    ->addAttributeToFilter(
                                        array(
                                            array('attribute' => 'sku', 'neq' => '1')

                                        )
                                    )
                                    ->setStoreId($storeId)
                                    ->addStoreFilter($storeId)
                                    ->setPageSize((int)$size);

                Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
                Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

                if($collection->getSize()) return $collection;
            }
        }

        return;
    }

    public function _getHelper(){
        return Mage::helper('jbp_featuredbycategory');
    }

    public function getProduct(){
        $id = $this->_getHelper()->getCookie();
        if($id){
            return Mage::getModel('catalog/product')->load($id);
        }
        return;
    }



}

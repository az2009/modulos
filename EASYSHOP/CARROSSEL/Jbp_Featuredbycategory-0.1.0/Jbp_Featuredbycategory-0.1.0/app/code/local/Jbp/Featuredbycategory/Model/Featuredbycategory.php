<?php
class Jbp_Featuredbycategory_Model_Featuredbycategory extends Mage_Core_Model_Abstract{
    
    public $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );        
    
    /**
     * Mais vendidos
     * @param array $data
     * @return unknown
     */
    public function getBestSellerProductCollection($data = array()){
        
        $storeId    = 1;
        $categoryId = $data['category_id'];
        $size       = $data['size'];
        $page       = $data['page'];		
        $toDate 	= date('y-m-d');
        $fromDate 	= date('y-m-1');
        
        $collection = Mage::getResourceModel('catalog/product_collection')        
                            ->addAttributeToSelect('*')
                            ->setStoreId($storeId)
                            ->addStoreFilter($storeId)
                            ->addAttributeToFilter('visibility', $this->visibility)
                            ->addAttributeToFilter('status', array('eq' => 1))
                            ->addTaxPercents()
                            ->addUrlRewrite()
                            ->joinField('qty',
                                'cataloginventory/stock_item',
                                'qty',
                                'product_id=entity_id',
                                '{{table}}.stock_id=1',
                                'left')
                            ->addAttributeToFilter('qty', array("gt" => 0))
                            ->setPageSize($size)
                            ->setCurPage($page);
        
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
		
		return $collection;
    }
    
    
    /**
     * Em oferta
     * @param array $data
     * @return void|unknown
     */
    public function getAllProductsPromotion($data = array()){
    
        $storeId    = 1;
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $categoryId = $data['category_id'];
        $size       = $data['size'];
        $page       = $data['page'];
    
    
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
                ->addAttributeToFilter('visibility', $this->visibility)
                ->addAttributeToFilter('status', array('eq' => 1))
                ->joinField('qty',
                    'cataloginventory/stock_item',
                    'qty',
                    'product_id=entity_id',
                    '{{table}}.stock_id=1',
                    'left')
                ->addAttributeToFilter('qty', array("gt" => 0))
                ->setPageSize($size)
                ->setCurPage($page);
                 
                if(($categoryId != 'all') && !empty($categoryId)){
                    $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                    ->addAttributeToFilter(array(array('attribute' => 'category_id', array('in' => array('finset' => $categoryId)))));
                }

                $collection->getSelect()->order('RAND()');

                return $collection;
                
    }
    
    /**
     * Você não sabia que vendia
     * @return void|unknown
     */
    public function getFeaturedProductCollection($data = array()){
    
        $storeId    = 1;
        $categoryId = $data['category_id'];
        $size       = $data['size'];
        $page       = $data['page'];
    
        $collection = Mage::getResourceModel('catalog/product_collection')
        ->addUrlRewrite()
        ->addAttributeToSelect('*')
        ->addAttributeToFilter('status','1')
        ->addAttributeToFilter('visibility', $this->visibility)
        ->setStoreId($storeId)
        ->addStoreFilter($storeId)
        ->joinField('qty',
            'cataloginventory/stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left')
        ->addAttributeToFilter('qty', array("gt" => 0))
        ->setPageSize((int)$size)
        ->setCurPage($page);

        if(($categoryId != 'all') && !empty($categoryId)){
            $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                       ->addAttributeToFilter(array(array('attribute' => 'category_id', array('in' => array('finset' => $categoryId)))));
        }

        return $collection;
    }
    
    /**
     * Mais clicados
     */
    public function getMostViewedProductCollection($data = array()){
    
        $storeId    = 1;
        $categoryId = $data['category_id'];
        $size       = $data['size'];
        $page       = $data['page'];
    
        $collection = Mage::getResourceModel('reports/product_collection')
        ->addOrderedQty()
        ->addAttributeToSelect('*')
        ->setStoreId($storeId)
        ->addStoreFilter($storeId)
        ->addViewsCount()
        ->setOrder('views_count', 'desc')
        ->addAttributeToFilter('status','1')
        ->addAttributeToFilter('visibility', $this->visibility)
        ->joinField('qty',
            'cataloginventory/stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left')
        ->addAttributeToFilter('qty', array("gt" => 0))
        ->setCurPage($page)
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

        $collection->getSelect()->group('e.entity_id');
        
        return $collection;
    }
    
    
    /**
     * Novos produtos
     * @param array $data
     */
    public function getNewProductCollection($data = array()){
    
        $storeId    = 1;
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $categoryId = $data['category_id'];
        $size       = $data['size'];
        $page       = $data['page'];
    

        $collection = Mage::getResourceModel('catalog/product_collection')
        ->addUrlRewrite()
        ->addAttributeToSelect('*')
        ->addAttributeToFilter('status','1')
        ->setStoreId($storeId)
        ->addStoreFilter($storeId)
        ->addAttributeToFilter('visibility', $this->visibility)
        ->joinField('qty',
            'cataloginventory/stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left')
        ->addAttributeToFilter('qty', array("gt" => 0))
        ->setPageSize((int)$size)
        ->setCurPage($page)
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
        
        return $collection;
    }
    
    /**
     * Mais desejados
     * @param array $data
     */
    public function getWhiteListProductCollection($data = array()) {
    
        $storeId    = 1;
        $categoryId = $data['category_id'];
        $size       = $data['size'];  
        $page       = $data['page'];
        
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
    
        $collection = Mage::getResourceModel('catalog/product_collection')
        ->addUrlRewrite()
        ->addAttributeToSelect('*')
        ->addAttributeToFilter('status','1')
        ->setStoreId($storeId)
        ->addStoreFilter($storeId)
        ->addAttributeToFilter('visibility', $this->visibility)
        ->joinField('qty',
            'cataloginventory/stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left')
        ->addAttributeToFilter('qty', array("gt" => 0))
        ->setPageSize($size)
        ->setCurPage($page);

        if(($categoryId != 'all') && !empty($categoryId)){
            $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                       ->addAttributeToFilter(array(array('attribute' => 'category_id', array('in' => array('finset' => $categoryId)))));
        }

        $collection->getSelect()->where('e.entity_id in ('.$this->getProductIdWhiteList().')', array());
        
        return $collection;
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
    
    /**
     * Te indicamos
     * @return void|unknown
     */
    public function getIndicamosProductCollection($data = array()){
    
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $storeId    = 1;
        $categoryId = $data['category_id'];
        $size       = $data['size'];
        $page       = $data['page'];
        $this->id   = $data['idprod'];
    
        $atr = explode(',', $this->_getHelper()->getAttributeProductsRandom());
    
        if($this->getProduct() && is_array($atr)){
    
            foreach ($atr as $attribute){
    
                $collection = Mage::getResourceModel('catalog/product_collection')
                ->addUrlRewrite()
                ->addAttributeToSelect('*')
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
                ->addAttributeToFilter('visibility', $this->visibility)
                ->joinField('qty',
                    'cataloginventory/stock_item',
                    'qty',
                    'product_id=entity_id',
                    '{{table}}.stock_id=1',
                    'left')
                ->addAttributeToFilter('qty', array("gt" => 0))
                ->setPageSize((int)$size)
                ->setCurPage($page);
    
                return $collection;
            }
        }
    
        return;
    }
    
    public function getGeneralProductCollection($data = array()){
        
        $storeId    = 1;
        $categoryId = $data['category_id'];
        
        $cat = Mage::getModel('catalog/category')->load($categoryId);
        
        $size       = $data['size'];
        $page       = $data['page'];
        $sort       = json_decode($data['general'], true);
        
        $general         = json_decode($data['general'],true);
        $attributeSelect = $data['general']['container']['attributeSelect'];
        
        $collection = Mage::getResourceModel('catalog/product_collection')
        ->addUrlRewrite()
        ->addAttributeToSelect('*')
        ->addAttributeToFilter('status','1')
        ->addAttributeToFilter('visibility', $this->visibility)                
        ->setStoreId($storeId)
        ->addStoreFilter($storeId)
        ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
        ->joinField('is_in_stock','cataloginventory/stock_item','is_in_stock','product_id=entity_id','{{table}}.stock_id=1','left')
        ->addAttributeToFilter('is_in_stock', array("eq" => 1))
        ->setPageSize((int)$size)
        ->setCurPage($page)
        ->setOrder($sort['container']['sort']['attribute'], $sort['container']['sort']['order']);
        
        if(($categoryId != 'all') && !empty($categoryId)){
            $collection->addAttributeToFilter(array(array('attribute' => 'category_id', array('in' => array('finset' => $categoryId)))));
            //$collection->addCategoryFilter($cat);
        }        
        foreach($general['container']['group'] as $k => $v){
            $collection->addAttributeToFilter($v['attribute'], array($v['condFilter'] => $v['value']));
        }           
        $collection->getSelect()->group('e.entity_id');

        return $collection;
    }
    
    public function _getHelper(){
        return Mage::helper('jbp_featuredbycategory');
    }
    
    public function getProduct(){
        if($this->id){
            return Mage::getModel('catalog/product')->load($this->id);
        }
        return;
    }

}
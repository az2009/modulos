<?php
class Jbp_Featuredbycategory_IndexController extends Mage_Core_Controller_Front_Action {

     public $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );

    public function indexAction(){
        die();
//         $customerId = Mage::getSingleton('customer/session')->getData();
//         echo"<pre>";
        
        $opt =  Mage::getResourceSingleton('customer/customer')->getAttribute('gender')->getSource()->getOptionText(1);
        
        var_dump($opt);
        
//         $customerId = Mage::getSingleton('customer/session')->getId();
        
//         $customer = Mage::getModel('customer/customer')->load($customerId)->getData();
        
//         echo "<pre>";
//         var_dump($customer);

        die();    
        
//         $qtdParce = Mage::helper('core')->jsonDecode(Mage::helper('trendsclub_theme')->getParcelaProducts());
//         echo end($qtdParce);


//         die();
//         $product = Mage::getModel('catalog/product')->load('519');

//         echo "<pre>";
//             var_dump($product->getData());



//         die();
        $data = array(
            'category_id' => 12,
            'size' => 10,
            'page' => 1
        );

        $col = Mage::getModel('jbp_featuredbycategory/featuredbycategory')->getGeneralProductCollection($data);

        $result = $col->getItems();

        echo "<pre>";
        var_dump($result);

        die();

        $orderId = 169;//(int)$this->getRequest()->getParam('order_id');

        $order = Mage::getModel('sales/order')->load($orderId);

        Mage::getSingleton('checkout/session')->setLastRealOrderId($order->getId());

        Mage::getSingleton('checkout/type_onepage')->getCheckout()
            ->setLastOrderId($order->getId())
            ->setLastSuccessQuoteId($order->getQuoteId())
            ->setLastQuoteId($order->getQuoteId());

        $this->_redirectUrl('/checkout/onepage/success/');

        return;

        die();

//         $storeId    = 1;
//         $categoryId = 41;
//         $size       = 5;
//         $page       = 4;
//         $sort       = json_decode($data['general'], true);

// //         $general         = json_decode($data['general'],true);
// //         $attributeSelect = $data['general']['container']['attributeSelect'];

//         $collection = Mage::getResourceModel('catalog/product_collection')
//         ->addUrlRewrite()
//         ->addAttributeToSelect('*')
//         ->addAttributeToFilter('status','1')
//         ->addAttributeToFilter('visibility', $this->visibility)
//         ->setStoreId($storeId)
//         ->addStoreFilter($storeId)
//         ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
//         ->joinField('qty','cataloginventory/stock_item','qty','product_id = entity_id','{{table}}.stock_id=1','left')
//         ->addAttributeToFilter('qty', array("gt" => 0))
//         ->setCurPage($page)
//         ->setPageSize((int)$size);
//         //->setOrder($sort['container']['sort']['attribute'], $sort['container']['sort']['order']);
//         if(($categoryId != 'all') && !empty($categoryId)){
//             $collection->addAttributeToFilter(array(array('attribute' => 'category_id', array('in' => array('finset' => $categoryId)))));
//         }
// //         foreach($general['container']['group'] as $k => $v){
// //             $collection->addAttributeToFilter($v['attribute'], array($v['condFilter'] => $v['value']));
// //         }
//         $collection->getSelect()->group('e.entity_id');



//         echo "<pre>";



//         var_dump($collection->getItems());

        die();

        $collection = Mage::getModel('catalog/product')
                            ->getCollection()
                            ->addAttributeToFilter('*');

        $con = Mage::getSingleton('core/resource');
        $read = $con->getConnection('core_read');
        $sql ="select product_id from wishlist_item group by product_id order by count(product_id) desc, product_id desc limit 0, 2";
        $result = $read->fetchAll($sql);

        echo "<pre>";
        var_dump($result);

        die();

        $collection->getSelect()
                   ->join('wishlist_item', '', array());
//                    ->where('entity_id in (5,4)', array())
//                    ->order('entity_id desc')
//                    ->limit(2)
//                    ->group('entity_id');

//                    echo $collection->getSelect();
//                    die();


        echo "<pre>";
        var_dump($collection->getData());


//         ini_set('display_errors', 1);
//         $block = $this->getLayout()->createBlock('jbp_featuredbycategory/featuredbycategory')->setData('category_id', 3);;

//         echo "<pre>";
//         var_dump($block->getNewProductCollection()->getData());
    }

}
<?php
class HC_FeedGoogleShopping_Model_Feedzoom extends HC_FeedGoogleShopping_Model_Feed {
    
    protected function _construct(){
        $this->_init('hc_feedgoogleshopping/feedzoom');
    }
    
    public function _getProductCollection(){
        $collection = Mage::getSingleton('catalog/product')
        ->getCollection()
        ->addAttributeToSelect('*')
        ->addAttributeToFilter(
            array(
                array('attribute' => 'status', 'eq' => 1)
            ))
            ->addAttributeToFilter(
                array(
                    array('attribute' => 'price', 'gt' => 0)
                ))
                ->addAttributeToFilter(
                    array(
                        array('attribute' => 'visibility', 'eq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH),
                        array('attribute' => 'visibility', 'eq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH),
                        array('attribute' => 'visibility', 'eq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG)
                    ))
                    ->addAttributeToFilter('vender_buscape', 1)
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->addAttributeToSort('created_at','desc')
                    ->joinField('qty',
                        'cataloginventory/stock_item',
                        'qty',
                        'product_id=entity_id',
                        '{{table}}.stock_id=1',
                        'left')
                        ->addAttributeToFilter('qty', array("gt" => 0))
                        ->load();
    
    
    
                        return $collection;
    }
    
    /**
     * Gera o Feed
     */
    public function feedGenerate(){
        
        if(!Mage::helper('hc_feedgoogleshopping')->getFeedConfig('feedgoogleshopping_general/feedzoom_enabled')) return;
        
        $xml  = '<?xml version="1.0" encoding="utf-8" ?><produtos>';
        
        try{
            
            foreach($this->_getProductCollection() as $item){
    
                $xml .= 
                '<produto>'
                    ."<codigo>".$item->getData($this->_helper()->getCfg('feedzoom_id'))."</codigo>"
                    ."<nome><![CDATA[".$item->getData($this->_helper()->getCfg('feedzoom_nome'))."]]></nome>"
                    ."<description><![CDATA[".$item->getData($this->_helper()->getCfg('feedzoom_description'))."]]></description>"
                        
                    ."<preco_de>".Mage::helper('core')->currency($item->getData('price'), false, false)."</preco_de>"
                    ."<preco>".Mage::helper('core')->currency($this->_helper()->getPriceSpecial($item), false, false)."</preco>"                   
                    ."<nparcela>".$this->_helper()->getCfg('feedzoom_nparcela')."</nparcela>"                
                    ."<vparcela>". Mage::helper('core')->currency( 
                        (
                            ($this->_helper()->getPriceSpecial($item) + (($this->_helper()->getPriceSpecial($item) * $this->_helper()->getCfg('feedzoom_vparcela_juros'))  /100))  / $this->_helper()->getCfg('feedzoom_nparcela')
                            
                        ), false, false) ."</vparcela>"                            
                    ."<url>".$item->getProductUrl()."?utm_source=zoom</url>"              
                    ."<url_imagem>".Mage::helper('catalog/image')->init($item, $this->_helper()->getCfg('feedzoom_url_imagem'))->resize(200)."</url_imagem>"                
                    ."<marca><![CDATA[".Mage::getModel('catalog/product')->load($item->getId())->getAttributeText($this->_helper()->getCfg('feedzoom_marca'))."]]></marca>"
                    .$this->_helper()->getCategoryZoom(Mage::getModel('catalog/product')->load($item->getId())->getAttributeText($this->_helper()->getCfg('feedzoom_cat')))
                    ."<sku>".$item->getData($this->_helper()->getCfg('feedzoom_sku'))."</sku>"                    
                .'</produto>';
            }
        }catch (Exception $e){
            Mage::log($e->getMessage());
        }
            
        $xml .='</produtos>';          
    
        file_put_contents('xmlzoom.xml', $xml);
    }
    
    /**
     * Retorna uma instancia da helper zoom
     * {@inheritDoc}
     * @see HC_FeedGoogleShopping_Model_Feed::_helper()
     */
    public function _helper(){
        return Mage::helper('hc_feedgoogleshopping/zoom');
    }
}

<?php
class HC_FeedGoogleShopping_Model_Feed extends Mage_Core_Model_Abstract {

    protected function _construct(){
        $this->_init('hc_feedgoogleshopping/feed');
    }
    
    /**
     * Retorna os produtos da loja
     * Retorna apenas os produtos simples e configuraveis
     * visivel no catalog e search ou somente em um dos dois
     */
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

            if(!Mage::helper('hc_feedgoogleshopping')->getFeedConfig('feedgoogleshopping_general/feedgoogleshopping_enabled')) return;
        
            $xml  ='<?xml version="1.0" encoding="utf-8" ?>'
                  .'<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">'
                  .'<channel>'
                  .'<title>'.$this->_helper()->getFeedConfig('feedgoogleshopping_general/feedgoogleshopping_title_feed').'</title>'
                  .'<link>'.$this->_helper()->getFeedConfig('feedgoogleshopping_general/feedgoogleshopping_link_feed').'</link>'
                  .'<description>'.$this->_helper()->getFeedConfig('feedgoogleshopping_general/feedgoogleshopping_description_feed').'</description>';

            foreach($this->_getProductCollection() as $item){

                $xml .= '<item>'
                        ."<g:id>".$item->getData($this->_helper()->getFeedConfig('feedgoogleshopping_attributes/feedgoogleshopping_id'))."</g:id>"
                        ."<g:title><![CDATA[".$item->getData($this->_helper()->getFeedConfig('feedgoogleshopping_attributes/feedgoogleshopping_title'))."]]></g:title>"
                        ."<g:description><![CDATA[".$item->getData($this->_helper()->getFeedConfig('feedgoogleshopping_attributes/feedgoogleshopping_description'))."]]></g:description>"
                        ."<g:price>".Mage::helper('core')->currency($item->getData($this->_helper()->getFeedConfig('feedgoogleshopping_attributes/feedgoogleshopping_price')), false, false)."</g:price>"
                        .$this->_helper()->isSpecialPrice($item->getData($this->_helper()->getFeedConfig('feedgoogleshopping_attributes/feedgoogleshopping_special_price')))
                        ."<g:availability>in stock</g:availability>"
                        ."<g:link>".$item->getProductUrl()."</g:link>"
                        ."<g:image_link>".Mage::helper('catalog/image')->init($item, $this->_helper()->getFeedConfig('feedgoogleshopping_attributes/feedgoogleshopping_image_link'))->resize(200)."</g:image_link>"
                        ."<g:condition>new</g:condition>"
                        ."<g:qty>".number_format($item->getQty(),0,'','')."</g:qty>"                           
                        ."<g:brand><![CDATA[".Mage::getModel('catalog/product')->load($item->getId())->getAttributeText($this->_helper()->getFeedConfig('feedgoogleshopping_attributes/feedgoogleshopping_brand'))."]]></g:brand>"
                        .$this->_helper()->isCategoryProduct(Mage::getModel('catalog/product')->load($item->getId())->getData($this->_helper()->getFeedConfig('feedgoogleshopping_attributes/feedgoogleshopping_google_product_category')))                        
                        .'<g:identifier_exists>FALSE</g:identifier_exists>'
                        .'<g:parcelamento>12</g:parcelamento>'
                        .$this->_helper()->isProductType($item->getData($this->_helper()->getFeedConfig('feedgoogleshopping_attributes/feedgoogleshopping_google_product_category')))
                        .'</item>';
            }

            //$xml .= "<total>{$x}</total>";
            $xml .='</channel></rss>';

            file_put_contents('google_shopping.xml', $xml);
    }


    /**
     * Retorna as categorias do google shopping
     * @return string
     */
    public function getCategoryGoogleShopping(){
        $data = file('http://www.google.com/basepages/producttype/taxonomy.pt-BR.txt');
        unset($data[0]);

        foreach($data as $item){
            $dados[] = '"'.trim($item).'"';
        }

        return implode(',',$dados);
    }

    /**
     * Retorna uma instancia da helper do módulo
     * @return Mage_Core_Helper_Abstract
     */
    public function _helper(){
        return Mage::helper('hc_feedgoogleshopping');
    }























}

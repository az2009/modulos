<?php
class Jbp_Featuredbycategory_Model_Api extends  Mage_Api_Model_Resource_Abstract {
    
    const TYPE_LOOP = 'featured';
    
    /**
     * @param unknown $typeLoop Tipo de loop que deseja pegar
     * @param unknown $size Tamanho da collection que deseja retornar
     * @param unknown $categoryId ID da categoria que deseja filtrar
     * @param unknown $page Numero da paginação
     * @param unknown $idprod ID do produto que que foi clicado
     * @return void[][]|unknown[][]
     */
    public function getproductbyfilter($typeLoop, $size, $categoryId, $page, $idprod = null, $general = null){
        
        $element = array(
            'category_id'   => $categoryId,
            'size'          => $size,
            'page'          => $page,
            'idprod'        => $idprod,
            'general'       => $general           
        );
        
        $collection = $this->getCollectionProduct($typeLoop, $element);
        
        return array('product_ids' => array($collection));        
    }
        
    public function getCollectionProduct($loop, $data = array()){
        
        $last = null;
        $col = null;
        
        switch ($loop){
            case 'bestseller':                                
                $result = $this->getModel()->getBestSellerProductCollection($data)->getItems();
                if(count($result)) return $result;
                return;
            break;
            
            case 'deals':
                $result = $this->getModel()->getAllProductsPromotion($data)->getItems();
                if(count($result)) return $result;
                return;
            break;
            
            case 'featured':
                $result = $this->getModel()->getFeaturedProductCollection($data)->getItems();
                if(count($result)) return $result;
                return;
            break;
            
            case 'mostviewed':
                $result = $this->getModel()->getMostViewedProductCollection($data)->getItems();
                if(count($result)) return $result;
                return;
            break;
            
            case 'newsproducts':
                $result = $this->getModel()->getNewProductCollection($data)->getItems();
                if(count($result)) return $result;
                return;
            break;
            
            case 'whitelistproducts':
                $result = $this->getModel()->getWhiteListProductCollection($data)->getItems();
                if(count($result)) return $result;
                return;
            break;
            
            case 'general':
                
                $col = $this->getModel()->getGeneralProductCollection($data);
                
                if($col->getSize() > $data['size']){
                    $last = $col->getSize() / $data['size'];
                    if($data['page'] > $last)return;
                    $result = $col->getItems();
                }else{
                    $result = $col->getItems();
                }
                
                if(count($result)) return $result;
                
                return;
                
            break;
            
            case 'recommended':                
                if($this->getModel()->getIndicamosProductCollection($data)!= null){
                    $result = $this->getModel()->getIndicamosProductCollection($data)->getItems();
                    if(count($result)) return $result;
                }
                return;
            break;
            
        }
    }
    
    public function getModel(){
        return Mage::getModel('jbp_featuredbycategory/featuredbycategory');
    }
    
    
}
<?php
class HC_FeedGoogleShopping_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Retorna o preço promociaonal caso o produto tenha o mesmo
     * @param unknown $price
     * @return string
     */
    public function isSpecialPrice($price){
        if($price > 0){
           return "<g:sale_price>".Mage::helper('core')->currency($price, false, false)."</g:sale_price>";
        }
    }
    
    public function isCategoryProduct($cat){
        if(!empty($cat)){
            return "<g:google_product_category><![CDATA[".$this->formatCategory($cat)."]]></g:google_product_category>";
        }
    }
    
    public function isProductType($cat){
        if(!empty($cat)){
            return "<g:product_type><![CDATA[{$cat}]]></g:product_type>";
        }
    }
    
    

    /**
     * Retorna as configurações do feed
     */
    public function getFeedConfig($cfg){
        return Mage::getStoreConfig("feedgoogleshopping_config/{$cfg}");
    }
    
    /**
     * Formata as categorias
     * @param unknown $string
     * @return NULL|string
     */
    public function formatCategory($string){
        $string = explode('>', $string);
        $x   = 0;
        $tag = null;
    
        while($x != 3){
             
            if($x == 0){
                $tag[] = $string[0];
            }elseif($x == 1){
                $tag[] =  $string[count($string) -2];
            }elseif($x == 2){
                $tag[] =  end($string);
            }    
            $x++;
        }
        return implode(' > ', $tag);
    
    }

    /**
     * Efetua o download do árquivo gerado
     * @param unknown $filename
     * @param unknown $contentType
     */
    public function displayFile($filename, $contentType) {

        header('Content-Type: '.$contentType);
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        $contentFile = Mage::getBaseDir().'/'.$filename;
        $modifiedTime = filemtime($contentFile);
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $modifiedTime).' GMT');
        header('Etag: '.md5($modifiedTime));
        readfile($contentFile);
    }


}
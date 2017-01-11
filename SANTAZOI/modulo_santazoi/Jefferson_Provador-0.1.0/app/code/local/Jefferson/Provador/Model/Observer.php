<?php
class Jefferson_Provador_Model_Observer {

    private $sku;
    private $codePay;
    private $productsToProv;
    private $productsNotProv;

    public function __construct(){
        $this->codePay = Mage::helper('provador')->getPayment();
        $this->sku     = Mage::helper('provador')->getSku();
    }


    /**
     * Caso tenha os dois tipos de produto no carrinho redireciona para o carrinho
     * solicitando a remoção de um dos tipos de produtos
     */
    public function checkCart(){

        if(!Mage::helper('provador')->getEnabled()){
            return;
        }

        $this->checkCartOnepage();

//         var_dump($this->productsNotProv);
//         die();

        if($this->productsToProv > 0 && $this->productsNotProv > 0){

            $url = Mage::getUrl('checkout/cart');

    		$response = Mage::app()->getFrontController()->getResponse();

    		Mage::getSingleton('core/session')->addError(
    			Mage::helper('provador')->__('No seu <strong>carrinho abandonado</strong> e <strong>atual</strong> existe produtos para <strong>Prova</strong> e para <strong>Compra direta</strong>, <strong>remova</strong> um dos
    			                             tipos de produtos para <strong>Finalizar o pedido</strong>'));

    		$response->setRedirect($url);

    		Mage::app()->getResponse()->sendResponse();
    		exit;

        }
    }


    /**
     * Inicia as validações
     * @param unknown $observer
     */
    public function executeValidation($observer){

        if(Mage::helper('provador')->getEnabled()){
                //verifica se existe produtos para prova no carrinho
                //verifica se o produto a ser adicionado não é de prova
                if($this->checkOptionsProductIncart() == true && $this->getItemBeforeAddToCart($observer) == false && $this->checkCartEmpty()){

                    Mage::throwException(Mage::helper('provador')->__('There is evidence in the cart products'));

                //verifica se não existe produtos para prova no carrinho
                //verifica se o produto a ser adicionado é de prova
                }elseif($this->checkOptionsProductIncart() == false && $this->getItemBeforeAddToCart($observer) == true && $this->checkCartEmpty()){

                    Mage::throwException(Mage::helper('provador')->__('There is no evidence that products in the cart'));
                }
        }
    }


    /**
     * Verifica se existe produtos selecionados para PROVA no carrinho
     */
    public function checkOptionsProductIncart(){

        $quote 	   = Mage::getModel('checkout/session')->getQuote();

        //pega os items do carrinho
        $cartItems = $quote->getAllItems();

            //verifica se o carrinho tem algum produto
            if($this->checkCartEmpty()){

                    //lista os items
                    foreach($cartItems as $item){

                        //pega as opções personalizadas e selecionadas do produto
                        $opt = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());

                            //verficia se o produto tem opções personalizadas
                            if(is_array($opt['options'])){

                                    foreach($opt['options'] as $k => $v){

                                        //executa uma query buscando os valores da opção personalizadas através do option_id
                                        $collection = Mage::getResourceModel('catalog/product_option_value_collection')
                                                            ->addFieldToFilter('option_id', $v['option_id'])
                                                            ->getValues(Mage::app()->getStore()->getStoreId());

                                            foreach ($collection as $item){

                                                //verifica se existe a opção personalziada para prova
                                                if(strtolower(trim($item->getSku())) == $this->sku){
                                                    //return $item->getSku();
                                                    return true;
                                                }
                                            }

                                    }

                            }
                    }
            }
            return false;
    }


    /**
     * Checa se existe produtos de prova e produtos que não é de prova no carrinho
     */

    public function checkCartOnepage(){
        $quote 	   = Mage::getModel('checkout/session')->getQuote();

        //pega os items do carrinho
        $cartItems = $quote->getAllItems();

        //verifica se o carrinho tem algum produto
        if($this->checkCartEmpty()){

            //lista os items
            foreach($cartItems as $item){

                //pega as opções personalizadas e selecionadas do produto
                $opt = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());







                //verficia se o produto tem opções personalizadas
                if(is_array($opt['options'])){

                    foreach($opt['options'] as $k => $v){

                        //executa uma query buscando os valores da opção personalizadas através do option_id
                        $collection = Mage::getResourceModel('catalog/product_option_value_collection')
                                            ->addFieldToFilter('option_id', $v['option_id'])
                                            ->getValues(Mage::app()->getStore()->getStoreId());

                        foreach ($collection as $item){

                            //verifica se existe a opção personalziada para prova
                            if(strtolower(trim($item->getSku())) == $this->sku){
                                $this->productsToProv++;
                            }else{
                                $this->productsNotProv++;
                            }
                        }

                    }

                }else{

                    $config = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($item->getProduct()->getId());

                    //Verifica se o produto simples não é filho de um configuravel
                    //Pois quando o produto e configuravel ele pega o configuravel e seus filhos
                    if (empty($config)){
                        $this->productsNotProv++;
                    }

                }
            }
        }

    }


    /**
     * Verifica se o produto a adicionar é para PROVA
     * @param unknown $observer
     * @return boolean
     */
    public function getItemBeforeAddToCart($observer){

        $opt = $observer->getProduct()->getTypeInstance(true)->getOrderOptions($observer->getProduct());

        if(is_array($opt['options'])){

            foreach($opt['options'] as $k => $v){

                $collection = Mage::getResourceModel('catalog/product_option_value_collection')
                                    ->addFieldToFilter('option_id', $v['option_id'])
                                    ->getValues(Mage::app()->getStore()->getStoreId());

                foreach ($collection as $item){
                    if(strtolower(trim($item->getSku())) == $this->sku){
                        return true;
                    }
                }
            }
        }
        return false;
    }


    /**
     * Verifica se o carrinho tem produtos adicionados
     * @return boolean
     */
    public function checkCartEmpty(){
        $quote 	   = Mage::getModel('checkout/session')->getQuote();
        if((int)$quote->getItemsQty()){
            return true;
        }

        return false;
    }


    /**
     * Desativa o método de pagamento especifico
     * @return Jefferson_Provador_Model_Observer
     */
    public function setPaymentMethods()
    {
        if(Mage::helper('provador')->getEnabled()){
            if($this->checkCartEmpty()){
                if($this->checkOptionsProductIncart()){

                    //Pega os métodos ativos
                    $active_methods = Mage::getSingleton('payment/config')->getActiveMethods();

                    //Desativa o método de pagamento informado
                    $this->setStoreConfig("payment/{$this->codePay}/active", 0);
                    return $this;
                }
            }
        }

    }


    /**
     * Pega o código da loja para desativar o método desejado
     * @param unknown $path
     * @param unknown $value
     * @return Jefferson_Provador_Model_Observer
     */
    public function setStoreConfig($path, $value)
    {
        $store  = Mage::app()->getStore();
        $store->setConfig($path, $value);
        return $this;
        $code   = $store->getCode();
        $config = Mage::getConfig();
        $store->setConfig("stores/$code/$path", $value);
        return $this;
    }






}
?>
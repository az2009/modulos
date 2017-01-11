<?php
class Jbp_Giftlist_Helper_Cart extends Mage_Checkout_Helper_Cart {


    /**
     * retorna a url do produto para a listagem de produtos da lista de casamento e view do produto
     * {@inheritDoc}
     * @see Mage_Checkout_Helper_Cart::getAddUrl()
     */
    public function getAddUrl($product, $additional = array())
    {
        $routeParams = array(
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->_getHelperInstance('core')
            ->urlEncode($this->getCurrentUrl()),
            'product' => $product->getEntityId(),
            Mage_Core_Model_Url::FORM_KEY => $this->_getSingletonModel('core/session')->getFormKey()
        );

        if (!empty($additional)) {
            $routeParams = array_merge($routeParams, $additional);
        }

        if ($product->hasUrlDataObject()) {
            $routeParams['_store'] = $product->getUrlDataObject()->getStoreId();
            $routeParams['_store_to_url'] = true;
        }

        if ($this->_getRequest()->getRouteName() == 'checkout'
            && $this->_getRequest()->getControllerName() == 'cart') {
                $routeParams['in_cart'] = 1;
            }

            return $this->_getUrl('listaCasamento/quote/add', $routeParams);
    }

    /**
     * Retorna o objeto da lista de casamento de acordo com o usuário logado
     */
    public function getDataListMarriage(){
        return Mage::getModel('jbp_giftlist/list')->load($this->_getHelperGiftlist()->getList());
    }

    /**
     * Retorna o Id do endereço da lista de casamento
     * @return boolean
     */
    public function getIdAddressListMarriage(){
        return Mage::getModel('jbp_giftlist/list')->load($this->_getHelperGiftlist()->getList())->getData('jbpgl_event_shipping_id');
    }

    /**
     * Retorna um objeto do endereço de entrega da lista de casamento
     * @throws Exception
     */
    public function getAddressShippingListMarriage(){

        $id = $this->getIdAddressListMarriage();

        if(!$id){throw new Exception('Não foi possível carregar o endereço da lista de casamento'); return;}

        return Mage::getModel('customer/address')->load($id)->getData();

    }

    /**
     * valida a quantidade que esta ser adicionada no carrinho
     * @param unknown $qty
     * @return number|unknown
     */
    public function verifyQty($qty){
        if($qty < 1){
            return 1;
        }
        return $qty;
    }

    /**
     * Verifica se existe saldo na lista de casamento
     * @param unknown $product
     * @param unknown $params
     */
    public function verifyBalanceAvaliable($product,$params){
        $qty = $this->verifyQty($params['qty']);
        $balanceAvaliable = Mage::app()->getLayout()->createBlock('jbp_giftlist/giftlist')->getSaldoDisponivelParaResgate(1);
        $totalProduct = ($this->verifyPrice($product) * $qty) + $params[$params['shippingRate']];
        if($balanceAvaliable >= $totalProduct) return true;
        return false;
    }

    /**
     * remove os caracteres string do preço do frete
     * @param unknown $price
     * @return string
     */
    public function formatShipping($price){
        return number_format(strip_tags(trim(filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT) / 100)) ,2,".",".");
    }

    /**
     * Verifica se o produto esa disponível em estoques
     * @param unknown $product
     * @param unknown $params
     */
    public function verifyAvailability($product, $params){

        if($product->isConfigurable()){
            $collection = Mage::getModel('catalog/product_type_configurable')
            ->getUsedProductCollection($product)
            ->addAttributeToSelect('*');
            foreach($params['super_attribute'] as $k => $v){
                $collection->addAttributeToFilter($k, array('eq' => $v));
            }
            if($collection->getData()){
                $stock = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct(Mage::getModel('catalog/product')->load($collection->getFirstItem()->getId()))->getQty();
                if($stock < $params['qty']){
                    return false;
                }
                return true;
            }

        }elseif($product->getTypeId() == 'simple'){
            $stock = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
            if($stock < $params['qty']){
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Retorna o preço final do produto
     * @param unknown $product
     */
    public function verifyPrice($product){
        if($product->getSpecialPrice()){
            return $product->getSpecialPrice();
        }
        return $product->getPrice();
    }


    /**
     * Pega todos os itens da ordem informado e retorna os produtos para o estoque
     * caso a ordem seja de credito lista de casamento
     * @param unknown $orderId
     */
    public function getQtyOfItemsByOrderIdAnd($orderId){
        $qty = 0;
        $items = Mage::getModel('sales/order_item')
                        ->getCollection()
                        ->addFieldToSelect(array('order_id','qty_ordered','product_id'))
                        ->addAttributeToFilter('order_id', array('eq'=>$orderId));

        foreach ($items as $item){
            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($item->getProductId());
            if ($stockItem->getId() > 0 and $stockItem->getManageStock()) {
                $qty = (int)$stockItem->getQty() + $item->getQtyOrdered();
                $stockItem->setQty($qty);
                $stockItem->setIsInStock((int)($qty > 0));
                $stockItem->save();
            }
        }

    }

    /**
     * Retorna o objeto da helper giftlist
     */
    public function _getHelperGiftlist(){
        return Mage::helper('jbp_giftlist');
    }

}
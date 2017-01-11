<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class Jbp_Giftlist_QuoteController extends Mage_Checkout_CartController {

    /**
     * Adiciona o produto que irá ser resgatado
     * {@inheritDoc}
     * @see Mage_Checkout_CartController::addAction()
     */
    public function addAction(){

        if (!$this->_validateFormKey()) {
            $this->_goBack();
            return;
        }

        $params = $this->getRequest()->getParams();

        try {

            $params['qty'] = 1;

            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            if (!$product) {
                $this->_goBack();
                return;
            }

            $this->getOrder()->createOrder($product, $params);

        }catch (Mage_Core_Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
        $this->_redirect('listaCasamento/index/minhalista/', array('item'=>'resgatarProdutos'));
    }

    /**
     * método que é executado para atualizar os status das orders de crédito da lista
     */
    public function cronAction(){
        Mage::getModel('jbp_giftlist/sales_order')->setStatusOrder();
    }

    /**
     * Retorna um objeto da model sales_order
     */
    public function getOrder(){
        return Mage::getModel('jbp_giftlist/sales_order');
    }

}
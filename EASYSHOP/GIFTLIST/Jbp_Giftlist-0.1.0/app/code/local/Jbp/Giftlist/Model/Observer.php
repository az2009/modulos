<?php

class Jbp_Giftlist_Model_Observer extends Magestore_RewardPoints_Model_Observer{

    /**
     * Verifica os itens do carrinho se são do mesmo tipo
     * @param unknown $observer
     */
    public function holdCartEasypoints($observer){

        if(!Mage::helper('jbp_giftlist/config')->isEnabled()) return $this;

        $params = $observer->getEvent()->getControllerAction()->getRequest()->getParams();


        if(!empty($params['listcasamento'])){
            if(!Mage::helper('jbp_giftlist')->getListById($params['listcasamento'])->getData('jbpgl_active')){
                Mage::getSingleton('core/session')->addError(Mage::helper('jbp_giftlist')->__('Vocês esta tentando burlar o sistema, mas eu tratei isso ;)'));
                Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('listaCasamento/index/pesquisarlista/'))->sendResponse();
                exit;
            }
        }

        if($this->_getHelperPrice()->checkItemCartBeforeAddListOnly($params)){
            if(
                ($this->_getHelperPrice()->checkItemCart() == 'contem' && $this->_getHelperPrice()->checkItemCartBeforeAdd($params) == 'contem')
                ||
                ($this->_getHelperPrice()->checkItemCart() == 'notcontem' && $this->_getHelperPrice()->checkItemCartBeforeAdd($params) == 'notcontem')
                ||
                ($this->_getHelperPrice()->checkItemCart() == 'cartempty')
                )
            {}else{
                Mage::getSingleton('core/session')->addError(
                    Mage::helper('jbp_giftlist')->__('Atenção - Não é permitido adicionar no carrinho produtos da lista de casamento e outras
                          categorias. Para adicionar o produto desejado remova todos os itens do seu carrinho e adicione o item desejado novamente.')
                    );
                Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('checkout/cart/'))->sendResponse();
                exit;
            }
        }else{
                Mage::getSingleton('core/session')->addError(
                    Mage::helper('jbp_giftlist')->__('Atenção - Não é permitido adicionar no carrinho produtos de múltiplas listas de casamento. É apenas permitido produtos de uma lista.')
                    );
                Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('checkout/cart/'))->sendResponse();
                exit;
            }
    }

    /**
     * Remove o blocos da tela do carrinho caso  o tipo de produto seja apenas para troca de pontos
     * @param unknown $observer
     */
    public function removeBlock($observer){
        if(!Mage::helper('jbp_giftlist/config')->isEnabled()) return $this;
        $idList = null;

        $idList = (int)Mage::app()->getRequest()->getParam('listcasamento');

        if($observer->getEvent()->getAction()->getFullActionName() == 'catalog_product_view' && !empty($idList)){

            $layout = $observer->getEvent()->getLayout();
            $layout->getUpdate()->addUpdate(
                '<reference name="product.info">
                    <action method="setTemplate" ifconfig="jbp_giftlist_config/jbp_giftlist_general_config/enable"><template>jbp/giftlist/viewProducts.phtml</template></action>
                    <action method="addBodyClass" block="root" ifconfig="jbp_giftlist_config/jbp_giftlist_general_config/enable">
            			<classname>theme-listcasamento</classname>
            		</action>
                </reference>
                <reference name="product.info.addtocart">
                    <remove name="buyOneClick" />
                </reference>'
                );
            $layout->generateXml();

        }
    }

    /**
     * Verifica se na tela de pagamento no carrinho existe produtos para compra e troca se sim retorna para
     * a tela do carrinho lançando a informação abaixo.
     */
    public function holdCartEasypointsCheckout(){
        if(!Mage::helper('jbp_giftlist/config')->isEnabled()) return $this;
        //$this->_getHelperPrice()->checkItemCartCheckoutOnlyEasypoints();
        if($this->_getHelperPrice()->checkItemCartCheckout()){
            Mage::getSingleton('core/session')->addError(
                Mage::helper('jbp_giftlist')->__(
                    'Atenção - voce possui no carrinho produtos para compra e produtos da lista de casamento, remova os produtos de troca ou compra do carrinho
                    para finalizar a compra.'
                    )
                );
            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('checkout/cart/'))->sendResponse();
            exit;
        }
    }

    /**
     * Seta o id e a mensagem para os noivos da lista de casamento antes de salvar a order
     * @param unknown $observer
     * @return Jbp_Giftlist_Model_Observer
     */
    public function setIdListMarriageOrder($observer){
        if(!Mage::helper('jbp_giftlist/config')->isEnabled()) return $this;
        if($this->_getHelperPrice()->checkItemCart() == 'contem'){
            $msg = Mage::app()->getRequest()->getPost('jbp_giftlist_msg');
            $order= $observer->getEvent()->getOrder();
            $order->setData('jbp_giftlist_id', $this->_getHelperPrice()->getIdListMarriage());
            $order->setData('jbp_giftlist_msg',$msg);
        }
        return $this;
    }

    /**
     * Adiciona um bloco com os detalhes da lista de casamento
     * @param unknown $observer
     */
    public function addOrderCustomBlock($observer){
        if(!Mage::helper('jbp_giftlist/config')->isEnabled()) return $this;
        $block = $observer->getBlock();
        if(($block->getNameInLayout() == 'order_info') && ($child = $block->getChild('giftlist.order.info'))){
            $transport = $observer->getTransport();
            if($transport){
                $html = $transport->getHtml();
                $html .= $child->toHtml();
                $transport->setHtml($html);
            }
        }
    }

    /**
     * Desabilita o método de pagamento crédito lista de casamento
     * {@inheritDoc}
     * @see Magestore_RewardPoints_Model_Observer::checkPayment()
     */
    public function checkPayment(){
        if(!Mage::helper('jbp_giftlist/config')->isEnabled()) return $this;
        $this->_getHelperPrice()->disabledPaymentmethodByCode('checkmo');
    }

    /**
     * Retorna um objeto da helper rules
     * {@inheritDoc}
     * @see Magestore_RewardPoints_Model_Observer::_getHelperPrice()
     */
    public function _getHelperPrice(){
        if(!Mage::helper('jbp_giftlist/config')->isEnabled()) return $this;
        return Mage::helper('jbp_giftlist/rules');
    }


}
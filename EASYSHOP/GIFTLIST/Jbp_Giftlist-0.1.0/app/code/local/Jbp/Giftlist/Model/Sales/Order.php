<?php
class Jbp_Giftlist_Model_Sales_Order extends Mage_Core_Model_Abstract {

    public $status = 'resgate_lista_casamento';
    public $state = 'processing';

    /**
     * Retorna um objeto quote
     */
    public function _getQuote(){
        return Mage::getModel('sales/quote')->setStoreId($this->getStoreId());
    }


    /**
     * Retorna code id da loja
     */
    public function getStoreId(){
        return Mage::app()->getStore('default')->getId();
    }

    /**
     * Verifica se tem o envio setado
     * @param unknown $params
     * @return boolean|unknown
     */
    public function checkParams($params){
        if(!$params['shippingRate']){
            return false;
        }
        return $params['shippingRate'];
    }

    /**
     * Cria a ordem de resgate
     * @param unknown $product
     * @param unknown $params
     */
    public function createOrder($product, $params){

            $list_id = null;
            if(!$this->_getHelper()->verifyBalanceAvaliable($product, $params)){
                Mage::throwException($this->_getHelper()->__('Saldo indisponível para resgatar esse produto'));
                return;
            }

            if(!$this->_getHelper()->verifyAvailability($product, $params)){
                Mage::throwException($this->_getHelper()->__('Produto sem estoque'));
                return;
            }

            if(!$this->_getHelperGiftlist()->getCustomer()->isLoggedIn()){
                Mage::throwException($this->_getHelper()->__('Efetue o login'));
                return;
            }

            if($this->checkParams($params) === false){
                Mage::throwException($this->_getHelper()->__('Selecione um método envio'));
                return;
            }

            $websiteId = 1;

            $store = 1;

            try{

                $store = Mage::app()->getStore();

                // Start New Sales Order Quote
                $quote = $this->_getQuote();

                $params = new Varien_Object($params);

                $quote->addProduct($product, $params);

                // Set Sales Order Quote Currency
                $quote->setCurrency('BRL');

                // Carrega o objeto customer
                $customer = $this->_getHelperGiftlist()->getCustomer()->getCustomer();

                // Assign Customer To Sales Order Quote
                $quote->assignCustomer($customer);

                // Configure Notification
                $quote->setSendConfirmation(1);

                // Set Sales Order Shipping Address
                $quote->getShippingAddress()->addData($this->_getHelper()->getAddressShippingListMarriage());
                $quote->getBillingAddress()->addData($this->_getHelper()->getAddressShippingListMarriage());

                // Set Sales Order Shipping Address
                $shippingAddress = $quote->getShippingAddress();

                // Collect Rates and Set Shipping & Payment Method
                $shippingAddress->setCollectShippingRates(true)
                                ->collectShippingRates()
                                ->setShippingMethod($this->checkParams($params))
                                ->setPaymentMethod('checkmo');
                $quote->getPayment()->importData(array('method' => 'checkmo'));
                $quote->collectTotals()->save();

//                 echo "<pre>";
//                 var_dump($quote->getData());
//                 die();

                // Create Order From Quote
                $service = Mage::getModel('sales/service_quote', $quote);

                $service->submitAll();

                $increment_id = $service->getOrder()->getRealOrderId();

                // Resource Clean-Up
                $quote = $customer = $service = null;

                $order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);

                $order->setData('jbp_giftlist_id', $this->_getHelper()->getDataListMarriage()->getData('jbpgl_id'))
                      ->setData('jbp_giftlist_resgate', 1);
                $order->setState($this->state);
                $order->setStatus($this->status);

                $message = $this->_getHelper()->__('Solicitação de resgate de produto:'.$this->_getHelper()->getDataListMarriage()->getData('jbpgl_id'));
                $order->addStatusToHistory($this->status, $message, true);
                $order->save();

//                 if(!$order->canInvoice()){
//                     Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
//                 }

//                 $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();


//                 if (!$invoice->getTotalQty()) {
//                     Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
//                 }

//                 $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
//                 $invoice->register();
//                 $transactionSave = Mage::getModel('core/resource_transaction')
//                                         ->addObject($invoice)
//                                         ->addObject($invoice->getOrder());

//                 $transactionSave->save();

                /**
                 * Seta o produto resgatado
                 */

                $list_id = $this->_getHelper()->getDataListMarriage()->getData('jbpgl_id');
                Mage::getModel('jbp_giftlist/item')->saveRansomed($product->getId(), $list_id);

                // Finished
                Mage::getSingleton('core/session')->addSuccess($this->_getHelper()->__('Produto resgatado com sucessso, solicitação:'.$increment_id));

            }catch(Mage_Core_Exception $e){
                Mage::log($e->getMessage());
                return;
            }
    }

    /**
     * Seta o status para credito de todos os pedidos que forem lista de casamento
     */
    public function setStatusOrder(){
        $collection = Mage::getResourceModel('sales/order_collection')
        ->addAttributeToSelect('*')
        ->addAttributeToFilter('jbp_giftlist_id', array('neq'=>0))
        ->addAttributeToFilter('status', array('eq'=>'processing'))
        ->addAttributeToFilter('jbp_giftlist_resgate', array('eq'=>0));
        try{
            foreach($collection as $item){
                Mage::getModel('sales/order')->load($item->getId())
                ->setState('credito_lista_casamento',true)
                ->setStatus('credito_lista_casamento')
                ->save();
                $this->_getHelper()->getQtyOfItemsByOrderIdAnd($item->getId());
                $this->sendEmailCustomerList($item->getId());
            }
        }catch (Exception $e){
            Mage::log($e->getMessage());
        }
    }


     /**
     * Dispara o e-mail para os usuario ono da lista de casamento
     * @param unknown $orderId
     */
    public function sendEmailCustomerList($orderId){
        $order = Mage::getModel('sales/order')->load($orderId);
        $listId = $order->getData('jbp_giftlist_id');
        $customerId = Mage::getModel('jbp_giftlist/list')->load($listId)->getData('jbpgl_customer_id');
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $emailNoivos = $customer->getEmail();
        $emailCredito = $order->getCustomerEmail();
        $nameCredito = $order->getCustomerFirstname().' '.$order->getCustomerLastname();
        $msgnoivos = $order->getData('jbp_giftlist_msg');
        $items = $this->mountElement($orderId);

        $subject = $this->_getHelperGiftlist()->__('Você recebeu um presente');
        if(!empty($msgnoivos)){
            $subject = $this->_getHelperGiftlist()->__('Você recebeu um presente e enviaram uma mensagem');
        }

        $data = array(
            'subject' => $this->_getHelperGiftlist()->__($subject),
            'template_id' => 'jbp_giftlist_email_template_msg_noivo',
            'mail_destinatario' => $emailNoivos,
            'log_error' => 'falha ao envia o e-mail para os noivos',
            'email_comprou' => $emailCredito,
            'name_comprou' => $nameCredito,
            'msg_noivos' => $msgnoivos,
            'items' => $items
        );
        $this->_getHelperGiftlist()->sendEmailGeneral($data);
    }

    /**
     * Renderiza em linha os itens do pedido informado
     * @param unknown $orderId
     * @return string
     */
    public function mountElement($orderId){
        $items = Mage::getModel('sales/order_item')
                    ->getCollection()
                    ->addAttributeToFilter('order_id',array('eq'=>$orderId));
        foreach($items as $item){
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            try{$img = (string)Mage::helper('catalog/image')->init($product, 'small_image')->keepFrame(true)->resize(150,100);}catch(Exception $e){}
            $html .=
            "<tr>
                <td><img src='".$img."' /></td>
                <td>".$product->getName()."</td>
             </tr>";
        }
        return $html;
    }

    /**
     * Retorna um objeto da helper cart
     */
    public function _getHelper(){
        return Mage::helper('jbp_giftlist/cart');
    }

    /**
     * Retorna um objeto da helper data
     */
    public function _getHelperGiftlist(){
        return Mage::helper('jbp_giftlist');
    }



}
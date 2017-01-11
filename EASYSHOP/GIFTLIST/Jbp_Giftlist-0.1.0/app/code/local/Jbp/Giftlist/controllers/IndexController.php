<?php
class Jbp_Giftlist_IndexController extends Mage_Core_Controller_Front_Action {

    public $action = array(
        'giftlist_index_index',
        'giftlist_index_criarListaPost'
    );

    /**
     * Verifica se o cliente esta logado
     */
    public function prDispatch(){
        $actionCurrent = Mage::app()->getFrontController()->getAction()->getFullActionName();
        if(!$this->_getHelper()->getCustomer()->isLoggedIn()){
            if(!in_array($actionCurrent, $this->action)){
                return true;
            }
        }
    }

    /**
     * Grava os produtos resgatados
     * método depreciado
     */
//     public function setProductsRansomedAction(){
//         $param = (int)$this->getRequest()->getParam('idprod');
//         if($param){
//             Mage::getModel('jbp_giftlist/item')->saveRansomed($param);
//         }

//         $this->_redirect('listaCasamento/index/minhalista/item/resgatarProdutos/');
//     }

    public function isEnabled(){
        if(!Mage::helper('jbp_giftlist/config')->isEnabled()){
            Mage::getSingleton('core/session')->addError('Módulo desativado');
            $this->_redirect('/');
            return;
        }
    }

    /**
     * Retorna as cidades de acordo com o estado passado
     */
    public function cidadesAction(){

        $this->isEnabled();

        $cod_estados = $this->getRequest()->getParam('cod_estados');
        if(!empty($cod_estados)){
            Mage::getModel('jbp_giftlist/autocomplete')->getCidades($cod_estados);
        }
    }

    /**
     * Retorna um objeto da model checkout/type_onepage
     */
    public function getOnepage(){
        return Mage::getSingleton('checkout/type_onepage');
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

        $subject = $this->_getHelper()->__('Você recebeu um presente');
        if(!empty($msgnoivos)){
            $subject = $this->_getHelper()->__('Você recebeu um presente e enviaram uma mensagem');
        }

        $data = array(
            'subject' => $this->_getHelper()->__($subject),
            'template_id' => 'jbp_giftlist_email_template_msg_noivo',
            'mail_destinatario' => $emailNoivos,
            'log_error' => 'falha ao envia o e-mail para os noivos',
            'email_comprou' => $emailCredito,
            'name_comprou' => $nameCredito,
            'msg_noivos' => $msgnoivos,
            'items' => $items
        );
        $this->_getHelper()->sendEmailGeneral($data);
    }

    /**
     * Monta o elemento de acorodo com a order informada
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
     * Método para testes do módulo
     */
    public function testeAction(){}

    /**
     * carrega a view da lista de acordo com o id passado
     */
    public function listaCasamentoProdutosAction(){
        $this->isEnabled();
        $this->prDispatch();
        $listId = (int)$this->getRequest()->getParam('id');
        if(!$listId || !$this->_getHelper()->getListById($listId)->getData('jbpgl_active')){
            Mage::getSingleton('core/session')->addError($this->__('Informe um ID válido'));
            $this->_redirect('listaCasamento/index/pesquisarlista/');
        }

        $this->loadLayout();
        $this->renderLayout();

    }

    /**
     * action do post que efetua a pesquisa da lista
     */
    public function pesquisarListaPostAction(){
        $this->isEnabled();
        $rq = $this->getRequest();
        if($rq->isPost()){
            $block = $this->getLayout()
                          ->createBlock('jbp_giftlist/giftlist')
                          ->setData('jbpgae_cidade',$rq->getPost('jbpgae_cidade'))
                          ->setData('jbpgae_estado',$rq->getPost('jbpgae_estado'))
                          ->setData('jbpgl_event_date', $this->_getHelper()->formatDate($rq->getPost('dataCasamento')))
                          ->setData('jbpgl_name_noivo_ambos',$rq->getPost('nomeCasal'))
                          ->setTemplate('jbp/giftlist/resultSearch.phtml')
                          ->toHtml();

            $body = array('body' => $block);

            echo Mage::helper('core')->jsonEncode($body);
        }
    }

    /**
     * carrega a view da tela de pesquisa da lista
     */
    public function pesquisarListaAction(){
        $this->isEnabled();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Action que remove o produto da lista
     */
    public function removeAction(){
        $this->isEnabled();
        if($this->prDispatch()){
            $this->_redirect('*/index/');
            return;
        }
        if($this->_getHelper()->checkIsValidList()){
            Mage::getSingleton('core/session')->addError('Você não possui listas criadas, crie uma lista');
            $this->_redirect('*/index/', array('login'=>1));
            return;
        }
        $productId = $this->getRequest()->getParam('item');
        if(!empty($productId)){
            Mage::getModel('jbp_giftlist/item')->removeData($productId);
            $this->_redirectUrl(Mage::getSingleton('core/session')->getLastUrl());
            return;
        }
        $this->_redirect('/');
    }

    /**
     * Action que adiciona o item na lista
     */
    public function addItemAction(){
        $this->isEnabled();
        if($this->prDispatch()){
            $this->_redirect('*/index/');
            return;
        }
        if($this->_getHelper()->checkIsValidList()){
            Mage::getSingleton('core/session')->addError('Você não possui listas criadas, crie uma lista');
            $this->_redirect('*/index/', array('login'=>1));
            return;
        }
        
        $productId = $this->getRequest()->getParam('id');
        if(!empty($productId)){
            Mage::getModel('jbp_giftlist/item')->saveData($productId);
            $this->_redirectUrl(Mage::getSingleton('core/session')->getLastUrl());
            return;
        }
        $this->_redirect('/');
    }

    /**
     * Carrega a view do painel de da lista
     */
    public function minhalistaAction() {
        $this->isEnabled();
        if($this->prDispatch()){
            $this->_redirect('*/index/');
            return;
        }
        if($this->_getHelper()->checkIsValidList()){
            //Mage::getSingleton('core/session')->addError('Crie uma lista');
            $this->_redirect('*/index/', array('login'=>1));
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    public function indexAction() {
        $this->isEnabled();
        if(!$this->_getHelper()->getCustomer()->isLoggedIn()){
            $this->_redirect('listaCasamento/index/pesquisarlista/',array('login'=>1));
            return;
        }
        if($this->_getHelper()->getList()){
            $this->_redirect('listaCasamento/index/minhalista/',array('item'=>'produtosDesejados'));
            return;
        }
        
        $this->loadLayout();
        $this->renderLayout();
    }

    public function editarListaAction(){
        $this->isEnabled();
        if($this->prDispatch()){
            $this->_redirect('*/index/');
            return;
        }
        if($this->_getHelper()->checkIsValidList()){
            Mage::getSingleton('core/session')->addError('Você não possui listas criadas, crie uma lista');
            $this->_redirect('*/index/', array('login'=>1));
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }


    public function criarListaPostAction(){
        $this->isEnabled();
        if($this->prDispatch()){
            $this->_redirect('*/index/');
            return;
        }
        if (!$this->_validateFormKey() || !$this->_getHelper()->getCustomer()->isLoggedIn()) {
            $msg['error'] = 'Chave expirada';
            return;
        }

        $rq = $this->getRequest();
        if($rq->isPost()){
            $post = $rq->getPost();
            $model = Mage::getModel('jbp_giftlist/list');
            $post['jbpgl_event_date'] = $this->_getHelper()->formatDate($post['jbpgl_event_date']);
            $model->setData('jbpgl_customer_id', Mage::getSingleton('customer/session')->getId());
            if($this->_getHelper()->validate($post, array('postcode', 'jbpgl_description'))){
                Mage::getSingleton('core/session')->addError($this->_getHelper()->validate($post, array('postcode', 'jbpgl_description')));
                $this->_redirect('*/', array('login' => 1));
                return;
            }
            $result = $model->saveData($post, $_FILES);
            if($result){
                $this->_redirect('*/*/minhalista/item/produtosDesejados/');
                return;
            }
            $this->_redirect('*/*/minhalista/item/produtosDesejados/');
            return;
        }
        $this->_redirect('*/*/minhaslistas', array('login' => 1));
    }

    public function _getHelper(){
        return Mage::helper('jbp_giftlist');
    }

    public function addAddressPostAction(){
        $this->isEnabled();
        if($this->prDispatch()){
            $this->_redirect('*/index/');
            return;
        }
        if (!$this->_validateFormKey() || !$this->_getHelper()->getCustomer()->isLoggedIn()) {
            $msg['error'] = 'Chave expirada';
            return;
        }

        $rq = $this->getRequest();

        if($rq->isPost()){

            $billing = $rq->getPost('billing');

            $billing['customer_id'] = $this->_getHelper()->getCustomer()->getId();

            $customer = Mage::getModel('customer/address');

            $customer->setCustomerId($this->_getHelper()->getCustomer()->getId())
                     ->setIsDefaultShipping('1')
                     ->setSaveInAddressBook('1');
            $customer->addData($billing);

            try{
                $customer->save();
                $msg['success'] = $this->__('Endereço cadastrado com sucesso');
                $msg['body'] =  $this->getLayout()->createBlock('jbp_giftlist/giftlist')->setTemplate('jbp/giftlist/listAddress.phtml')->toHtml();
            }catch(Exception $e){
                $msg['error'] = $e->getMessage();
            }

        }

        if($msg['error']){
            $code = '403';
            $response['error'] = $msg['error'];
        }else{
            $code = '200';
            $response['success'] = $msg['success'];
            $response['body'] = $msg['body'];
        }

        $this->getResponse()->setHeader('HTTP/1.0',$code,true);

        $data = Mage::helper('core')->jsonEncode($response);

        $this->getResponse()->setBody($data);

        $this->getResponse()->sendResponse();
        exit();


    }

    public function loginPostAction(){
        $this->isEnabled();
        $message = null;
        $success = null;
        $response = null;
        $data = null;

        if (!$this->_validateFormKey()) {
            $message = 'Chave expirada';
            return;
        }

        $session = $this->_getHelper()->getCustomer();


        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost();
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    $success = 'ok';
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.');
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                } catch (Exception $e) {}
            } else {
                $message = $this->__('Login and password are required.');
            }
        }


        if($message){
            $code = '403';
            $response['error'] = $message;
        }else{
            $code = '200';
            $response['success'] = $success;
        }

        $this->getResponse()->setHeader('HTTP/1.0',$code,true);

        $data = Mage::helper('core')->jsonEncode($response);

        $this->getResponse()->setBody($data);

        $this->getResponse()->sendResponse();
        exit();

    }

    public function viewlistAction(){
        
        $this->isEnabled();
        if($this->prDispatch()){
            $this->_redirect('*/index/');
            return;
        }
        
        $param = $this->getRequest()->getParam('id');
        $item = $this->getRequest()->getParam('item');
        
        if(!$param || !$item){
            $this->_redirect('listaCasamento/index/minhaslistas');
            return;
        }
        
        if($param == $this->_getHelper()->getLisActive()){
            $this->_redirect('listaCasamento/index/minhalista');
            return;
        }
        
        if($item == 'resgatarProdutos'){
            $this->_redirect('listaCasamento/index/viewlist/', array('id' => $param, 'item' => 'produtosDesejados'));
            return;
        }
        
        $this->loadLayout();
        $this->renderLayout();              
    }
    
    public function minhaslistasAction(){
        $this->isEnabled();
        if($this->prDispatch()){
            $this->_redirect('*/index/');
            return;
        }
//         if($this->_getHelper()->checkIsValidList()){
//             //Mage::getSingleton('core/session')->addError('Crie uma lista');
//             $this->_redirect('listaCasamento/index/minhaslistas');
//             return;
//         }
        $this->loadLayout();
        $this->renderLayout();
    }


    public function novalistaAction(){
        
        $this->isEnabled();
        if($this->prDispatch()){
            $this->_redirect('*/index/');
            return;
        }
        
        if(!$this->_getHelper()->getList()){
            $this->_redirect('*/', array('login' => 1));
            return;
        }
        
        try{                                
            Mage::getModel('jbp_giftlist/list')->closedList();
            Mage::getSingleton('core/session')->addSuccess($this->__('Lista finalizada com sucesso'));                
        }catch(Exception $e){
            Mage::getSingleton('core/session')->addError($this->__('Ocorreu um erro tentar finalizar a lista'));
        }
        
        $this->_redirect('listaCasamento/index/minhaslistas');
    }
    
    


    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
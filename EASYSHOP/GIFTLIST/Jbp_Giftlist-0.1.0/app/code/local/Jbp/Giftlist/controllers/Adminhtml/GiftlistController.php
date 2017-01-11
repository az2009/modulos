<?php
class Jbp_Giftlist_Adminhtml_GiftlistController extends Mage_Adminhtml_Controller_Action {
    
    public function indexAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function editAction(){
        
        $id = (int)$this->getRequest()->getParam('id');
        $model = Mage::getModel('jbp_giftlist/list')->load($id);
        
        if(!$id || !$model->getId()){
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jbp_giftlist')->__('Lista inválida'));
            $this->_redirect('*/*/');
            return;
        }        
        Mage::register('list_marriage',$model->getId());               
        $this->loadLayout();        
        $this->renderLayout();
    }
    
    public function gridAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function gridcreditoAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function gridprodutosAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function gridresgateAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function promolistmarriageAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    
}

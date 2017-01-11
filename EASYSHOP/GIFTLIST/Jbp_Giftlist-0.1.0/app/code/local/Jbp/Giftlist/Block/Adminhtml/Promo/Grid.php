<?php
class Jbp_Giftlist_Block_Adminhtml_Promo_Grid extends Mage_Adminhtml_Block_Promo_Quote_Grid {
    
    /**
     * Add websites to sales rules collection
     * Set collection
     *
     * @return Mage_Adminhtml_Block_Promo_Quote_Grid
     */
    public function _prepareCollection()
    {
        parent::_prepareCollection();
        /** @var $collection Mage_SalesRule_Model_Mysql4_Rule_Collection */
        $collection = Mage::getModel('salesrule/rule')
                            ->getResourceCollection();
        
        $collection->addFieldToFilter('cupom_lista_casamento', array('eq'=>'list_marriage'));
        $collection->addWebsitesToResult();
        $this->setCollection($collection);    
        return $this;
    }
    
    public function getRowUrl($row){        
        return $this->getUrl('*/promo_quote/edit', array('id' => $row->getId()));    
    }
    
}
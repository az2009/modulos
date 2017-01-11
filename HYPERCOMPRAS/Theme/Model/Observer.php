<?php
class HC_Theme_Model_Observer {

    public $collection;

    /**
     * Adiciona uma coluna customizada ao grid de pedidos
     * @param Varien_Event_Observer $observer
     */
    public function coreBlockAbstractToHtmlBefore($observer)
    {

        $block = $observer->getEvent()->getBlock();
        if ($block->getId() == 'sales_order_grid') {

            
            $block->addColumnAfter(
                'mercadolivre_order_id',
                array(
                    'header'   => Mage::helper('sales')->__('Mercado Livre ID'),
                    'align'    => 'center',
                    'type'     => 'text',
                    'index'    => 'mercadolivre_order_id',
                    'filter_condition_callback' => array($this,'_filterMercadoLivre')
                ),
                'mercadolivre_order_id'
            );

            $block->addColumnAfter(
                'customer_email',
                array(
                    'header'   => Mage::helper('sales')->__('E-mail'),
                    'type'     => 'text',
                    'index'    => 'customer_email',
                ),
                'created_at'
            );
            
            $block->sortColumnsByOrder();
        }
    }


    /**
     * Adiciona o filtro correspondente a coluna customizada
     * @param unknown $collection
     * @param unknown $column
     * @return HC_Theme_Model_Observer
     */
    public function _filterMercadoLivre($collection, $column){

        if (!$value = $column->getFilter()->getValue()){
            return $this;
        }

        $collection->getSelect()->where("thirdlevel_mercadolivre_order.mercadolivre_order_id like ?", "%$value%");

        return $this;

    }

    public function salesOrderGridCollectionLoadBefore($observer)
    {        
        $collection = $observer->getOrderGridCollection();
        
        $select = $collection->getSelect();
        
        $select->joinLeft('thirdlevel_mercadolivre_order','(main_table.increment_id = thirdlevel_mercadolivre_order.order_incremental_id)',array('mercadolivre_order_id'));
        
        $select->join('sales_flat_order', 'main_table.entity_id = sales_flat_order.entity_id',array('customer_email'));

        $this->collection = $observer->getOrderGridCollection();
    }
}



























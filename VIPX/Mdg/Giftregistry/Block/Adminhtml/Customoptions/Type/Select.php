<?php
    class Mdg_Giftregistry_Block_Adminhtml_Customoptions_Type_Select
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Select {

        public function __construct()
        {
            parent::__construct();
            $this->setTemplate('jbp/type/select.phtml');
            $this->setCanEditPrice(true);
            $this->setCanReadPrice(true);
        }

    }

?>
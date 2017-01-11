<?php
class Jefferson_Provador_Model_System_Config_Source_Payments extends Mage_Adminhtml_Model_System_Config_Source_Category{

	protected function _construct()
    {
        $this->_init('provador/system_config_source_category');
    }


    public function toOptionArray($addEmpty = true)
    {
       $_payments = Mage::getSingleton('payment/config')->getActiveMethods();
       $_methods  = array();
       foreach ($_payments as $_paymentCode=>$_paymentModel) {
            $_paymentTitle = Mage::getStoreConfig('payment/'.$_paymentCode.'/title');
            $_methods[$_paymentCode] = array(
                'label'     => $_paymentTitle,
                'value'     => $_paymentCode,
            );
        }

        return $_methods;
    }
}


?>
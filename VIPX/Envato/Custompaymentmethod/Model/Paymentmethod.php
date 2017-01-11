<?php
// app/code/local/Envato/Custompaymentmethod/Model/Paymentmethod.php
class Envato_Custompaymentmethod_Model_Paymentmethod extends Mage_Payment_Model_Method_Abstract {
  protected $_code  = 'custompaymentmethod';
  protected $_formBlockType = 'custompaymentmethod/form_custompaymentmethod';
  protected $_infoBlockType = 'custompaymentmethod/info_custompaymentmethod';

  public function assignData($data)
  {
    $info = $this->getInfoInstance();

    if ($data->getCustomFieldOne())
    {
      $info->setCustomFieldOne($data->getCustomFieldOne());
    }

    if ($data->getCustomFieldTwo())
    {
      $info->setCustomFieldTwo($data->getCustomFieldTwo());
    }

    if ($data->getCustomFieldThree())
    {
        $info->setCustomFieldThree($data->getCustomFieldThree());
    }

    return $this;
  }

  public function validate()
  {
    parent::validate();
    $info = $this->getInfoInstance();
    $grandTotal = Mage::helper('checkout')->getQuote()->getGrandTotal();

    if (!$info->getCustomFieldOne())
    {
      $errorCode = 'invalid_data';
      $errorMsg = $this->_getHelper()->__("Ecolha um tipo de pagamento.\n");
    }


    if (
        !$info->getCustomFieldTwo() && $info->getCustomFieldThree() == 'sim'
        || $info->getCustomFieldTwo() == 'Sem Troco' && $info->getCustomFieldThree() == 'sim'
        || $info->getCustomFieldTwo() == 'R$ 0,00' && $info->getCustomFieldThree() == 'sim'
        || $info->getCustomFieldTwo() == 'Informe o valor' && $info->getCustomFieldThree() == 'sim'

        )
    {
      $errorCode = 'invalid_data';
      $errorMsg .= $this->_getHelper()->__('Informe o valor com que irá efetuar o pagamento ');
    }

    if($info->getCustomFieldOne() != 'Dinheiro - Pagar em dinheiro(Com Troco)'){
        $info->setCustomFieldTwo('');
    }

    if ($errorMsg)
    {
      Mage::throwException($errorMsg);
    }

    return $this;
  }

  public function getOrderPlaceRedirectUrl()
  {
      return Mage::getUrl('custompaymentmethod/payment/gateway', array('_secure' => false));
    //return Mage::getUrl('custompaymentmethod/payment/redirect', array('_secure' => false));
  }
}
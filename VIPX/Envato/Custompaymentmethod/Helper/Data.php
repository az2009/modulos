<?php
// app/code/local/Envato/Custompaymentmethod/Helper/Data.php
class Envato_Custompaymentmethod_Helper_Data extends Mage_Core_Helper_Abstract
{
  public function getPaymentGatewayUrl()
  {
    return Mage::getUrl('custompaymentmethod/payment/gateway', array('_secure' => false));
  }

  public function getBandeirasCredito(){
    $bandeiras = explode(',',Mage::getStoreCOnfig('payment/custompaymentmethod/bandeira_credito'));
    return $bandeiras;
  }

  public function getBandeirasDebito(){
      $bandeiras = explode(',',Mage::getStoreCOnfig('payment/custompaymentmethod/bandeira_debito'));
      return $bandeiras;
  }

  public function getBandeirasVale(){
      $bandeiras = explode(',',Mage::getStoreCOnfig('payment/custompaymentmethod/bandeira_vale'));
      return $bandeiras;
  }

}
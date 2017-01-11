<?php
// app/code/local/Envato/Custompaymentmethod/Block/Form/Custompaymentmethod.php
class Envato_Custompaymentmethod_Block_Form_Custompaymentmethod extends Mage_Payment_Block_Form
{
  protected function _construct()
  {
    parent::_construct();
    $this->setTemplate('custompaymentmethod/form/custompaymentmethod.phtml');
  }

  public function mountHtmlCredito(){
      $html  = '';
      $array = array();
      $array = Mage::helper('custompaymentmethod')->getBandeirasCredito();
      if(!is_array($array)) return;
      foreach($array as $item){
          $html .= '<li><label><input type="radio" name="payment[custom_field_one]" value="Crédito - '.$item.'" /> '.$this->getBandeiraImg($item).' Crédito - '.$item.'</label></li>';
      }
      return $html;
  }

  public function mountHtmlDebito(){
      $html  = '';
      $array = array();
      $array = Mage::helper('custompaymentmethod')->getBandeirasDebito();
      if(!is_array($array)) return;
      foreach($array as $item){
          $html .= '<li><label><input type="radio" name="payment[custom_field_one]" value="Débito - '.$item.'" /> '.$this->getBandeiraImg($item).' Débito - '.$item.'</label></li>';
      }
      return $html;
  }

  public function mountHtmlVale(){
      $html  = '';
      $array = array();
      $array = Mage::helper('custompaymentmethod')->getBandeirasVale();
      if(!is_array($array)) return;
      foreach($array as $item){

          if($item == 'TICKET RESTAURANTE'){
              $labelExtra = '<label class="label-extra-ticket">(ATÉ R$250,00)</label>';
          }

          $html .= '<li><label><input type="radio" name="payment[custom_field_one]" value="Vale - '.$item.'" /> '.$this->getBandeiraImg($item).' Vale - '.$item.'</label>'.$labelExtra.'</li>';
      }
      return $html;
  }


  public function getBandeiraImg($label){
      switch ($label){
          case 'AMERICAN EXPRESS' :
              $img = '<img src="'.Mage::getDesign()->getSkinUrl('/envato/custompaymentmethod/image/RAM.png').'" class="img-bandeira" />';
          break;

          case 'DINERS' :
              $img = '<img src="'.Mage::getDesign()->getSkinUrl('/envato/custompaymentmethod/image/DNREST.png').'" class="img-bandeira" />';
          break;

          case 'ELO' :
              $img = '<img src="'.Mage::getDesign()->getSkinUrl('/envato/custompaymentmethod/image/REC.png').'" class="img-bandeira" />';
          break;

          case 'MASTERCARD' :
              $img = '<img src="'.Mage::getDesign()->getSkinUrl('/envato/custompaymentmethod/image/RDREST.png').'" class="img-bandeira" />';
          break;

          case 'VISA' :
              $img = '<img src="'.Mage::getDesign()->getSkinUrl('/envato/custompaymentmethod/image/VSREST.png').'" class="img-bandeira" />';
          break;

          case 'ALELO REFEIÇÃO/VISA VALE' :
              $img = '<img src="'.Mage::getDesign()->getSkinUrl('/envato/custompaymentmethod/image/VVREST.png').'" class="img-bandeira" />';
          break;

          case 'SODEXO' :
              $img = '<img src="'.Mage::getDesign()->getSkinUrl('/envato/custompaymentmethod/image/RSODEX.png').'" class="img-bandeira" />';
          break;

          case 'TICKET RESTAURANTE' :
              $img = '<img src="'.Mage::getDesign()->getSkinUrl('/envato/custompaymentmethod/image/TRE.png').'" class="img-bandeira" />';
          break;

          case 'MASTERCARD/MAESTRO' :
              $img = '<img src="'.Mage::getDesign()->getSkinUrl('/envato/custompaymentmethod/image/MEREST.png').'" class="img-bandeira" />';
          break;

          case 'VISA/ELECTRON' :
              $img = '<img src="'.Mage::getDesign()->getSkinUrl('/envato/custompaymentmethod/image/VIREST.png').'" class="img-bandeira" />';
          break;

      }
      return $img;
  }



}
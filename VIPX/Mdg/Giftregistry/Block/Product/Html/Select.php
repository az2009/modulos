<?php
     class Mdg_Giftregistry_Block_Product_Html_Select
     extends Mage_Core_Block_Html_Select {

         public function getDetails(){

             if($this->getData('details') == ''){
                 return;
             }elseif($this->getData('details') !='' && $this->getData('modo') != ''){

                 $data = "data-modo-sku=".$this->getData('details');

             }else{
                 $data = "data-sku=".$this->getData('details');
             }

             return $data;

         }

         public function getModo(){

             if($this->getData('modo') == ''){
                 return;
             }

             $data = "data-modo=".$this->getData('modo');

             return $data;
         }

         public function checkModo(){
             if($this->getModo() != ''){
                 $modo = 'select-modo';
             }else{
                 $modo = '';
             }

             return $modo;
         }

         protected function _toHtml()
         {
             if (!$this->_beforeToHtml()) {
                 return '';
             }

             $values = $this->getValue();

             if($this->getData('modo') != ''){

                 $html = '<div class="container-modo-select" '.$this->getDetails().'  '.$this->getModo().'><label>'. $this->getData('ti').'</label> </br>';

                 $html .= '<select '.$this->getDetails().' '.$this->getModo().' " name="' . $this->getName() . '" id="' . $this->getId() . '" class="'
                     . $this->getClass().' select-options-custom '.$this->checkModo().'" title="' . $this->getTitle() . '" ' . $this->getExtraParams() . '>';


             }else{
                 $html = '<select '.$this->getDetails().' '.$this->getModo().' " name="' . $this->getName() . '" id="' . $this->getId() . '" class="'
                     . $this->getClass().' select-options-custom '.$this->checkModo().'" title="' . $this->getTitle() . '" ' . $this->getExtraParams() . '>';
             }

//              $html = '<div '.$this->getData('modo').'-div >  <select '.$this->getDetails().' '.$this->getModo().' " name="' . $this->getName() . '" id="' . $this->getId() . '" class="'
//                  . $this->getClass().' select-options-custom '.$this->checkModo().'" title="' . $this->getTitle() . '" ' . $this->getExtraParams() . '>';


             if (!is_array($values)){
                 if (!is_null($values)) {
                     $values = array($values);
                 } else {
                     $values = array();
                 }
             }

             $isArrayOption = true;
             foreach ($this->getOptions() as $key => $option) {
                 if ($isArrayOption && is_array($option)) {
                     $value  = $option['value'];
                     $label  = (string)$option['label'];
                     $params = (!empty($option['params'])) ? $option['params'] : array();
                 } else {
                     $value = (string)$key;
                     $label = (string)$option;
                     $isArrayOption = false;
                     $params = array();
                 }

                 if (is_array($value)) {
                     $html .= '<optgroup label="' . $label . '">';
                     foreach ($value as $keyGroup => $optionGroup) {
                         if (!is_array($optionGroup)) {
                             $optionGroup = array(
                                 'value' => $keyGroup,
                                 'label' => $optionGroup
                             );
                         }
                         $html .= $this->_optionToHtml(
                             $optionGroup,
                             in_array($optionGroup['value'], $values)
                         );
                     }
                     $html .= '</optgroup>';
                 } else {
                     $html .= $this->_optionToHtml(
                         array(
                             'value' => $value,
                             'label' => $label,
                             'params' => $params
                         ),
                         in_array($value, $values)
                     );
                 }
             }

             if($this->getData('modo') != ''){
                 $html .= '</select> </div>';
             }else{
                 $html .= '</select>';
             }



             return $html;
         }

     }

?>
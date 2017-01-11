<?php
    class Envato_Custompaymentmethod_Model_System_Config_Source_Credito{

        public function toOptionArray($addEmpty = true)
        {
            $options = array();
            $bandeira = array(
                    'AMERICAN EXPRESS',
                    'DINERS',
                    'ELO',
                    'MASTERCARD',
                    'VISA'
                );

            foreach($bandeira as $item){
                $options[] = array(
                    'label' => $item,
                    'value' => $item
                );
            }

            return $options;
        }


    }

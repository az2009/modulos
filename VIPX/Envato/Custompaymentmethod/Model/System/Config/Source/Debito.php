<?php
    class Envato_Custompaymentmethod_Model_System_Config_Source_Debito{

        public function toOptionArray($addEmpty = true)
        {
            $options = array();
            $bandeira = array(
                    'ELO',
                    'MASTERCARD/MAESTRO',
                    'VISA/ELECTRON'
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

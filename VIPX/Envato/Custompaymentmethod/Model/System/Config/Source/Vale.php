<?php
    class Envato_Custompaymentmethod_Model_System_Config_Source_Vale{

        public function toOptionArray($addEmpty = true)
        {
            $options = array();
            $bandeira = array(
                    'ALELO REFEIÇÃO/VISA VALE',
                    'SODEXO',
                    'TICKET RESTAURANTE',
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

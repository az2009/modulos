<?php
class HC_Allin_Model_Observer {

    ###TRANSACIONAL - Envio###

        /**
         * Efetua o disparo do email
         * @param unknown $observer
         */
        public function sendMail($observer){

            #Retorna uma instancia da API
            $resource = $this->_resourceApi();

            #parametro para criar o template de envio
            $dataCreateHtml = array(
                'html'       => $this->_resourceApi()->_helper()->convertBase64($observer->getHtml()),
                'nm_html'    => $observer->getSubject().'_'.$this->_resourceApi()->_helper()->getCodeId(),
                "temporario" => "1",
            );

            /**
             * Cria o template html temporario e retorna o ID do mesmo
             * @var unknown
             */

            #Verifica se o envio via API esta liberado
            if(!$this->_resourceApi()->_helper()->getMethodSend()){
                $result = $resource->createHtml($dataCreateHtml)
                                   ->getHtmlId();

            }

            #parametros para enviar o e-mail
            $dataSendEmail = array(
                "nm_email"          => $observer->getData('email_destinatario'),
                "html_id"           => $result,
                "nm_subject"        => $observer->getData('subject'),

                "nm_remetente"      => $observer->getData('nome_remetente'),
                "email_remetente"   => $observer->getData('email_remetente'),
                "nm_reply"          => $observer->getData('email_reply'),

                "dt_envio"          => Mage::getModel('core/date')->date('Y-m-d'),
                "hr_envio"          => Mage::getModel('core/date')->date('h:i:s'),

                "campos"            => "",
                "valor"             => "",
            );

            /**
             * Dispara o envio
             */

            #Verifica se o envio via API esta liberado
            if(!$this->_resourceApi()->_helper()->getMethodSend()){
                $body = $resource->sendEmail($dataSendEmail);
                Mage::log($body,null,'logAllin.log');
            }

        }

    ###TRANSACIONAL - Envio###

    #INTÂNCIAS#

        /**
         * Retorna uam instancia da api
         */
        public function _resourceApi(){
            return Mage::getModel('hc_allin/api');
        }

        /**
         * Retorna uma instância da helper do módulo
         */
        public function _helper(){
            return Mage::helper('hc_allin');
        }
}
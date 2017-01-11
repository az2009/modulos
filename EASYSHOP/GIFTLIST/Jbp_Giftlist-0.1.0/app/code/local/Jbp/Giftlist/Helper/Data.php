<?php
class Jbp_Giftlist_Helper_Data extends Mage_Core_Helper_Abstract {


    /**
     * Retorna as lebels dos inputs
     * @param unknown $case
     * @return string
     */
    public function getLabel($case){
        switch($case){
            case 'jbpgl_name_noivo':
                return 'Nome do noivo';
            break;

            case 'jbpgl_name_noiva':
                return 'Nome da noiva';
            break;

            case 'jbpgl_event_date':
                return 'Data do evento';
            break;

            case 'jbpgae_estado':
                return 'Estado';
            break;

            case 'jbpgae_cidade':
                return 'Cidade';
            break;

            case 'jbpgl_description':
                return 'Mensagem do casal';
            break;

            case 'jbpgl_event_shipping_id':
                return 'Endereço de entrega';
            break;

            case 'jbpgl_termos':
                return 'Termos de uso';
            break;

        }
    }

    /**
     * Retoran uma collection dos itens da lista informada
     */
    public function checkItemTotal(){
        $collection = Mage::getModel('jbp_giftlist/item')
        ->getCollection()
        ->addFieldToFilter('jbpgi_list_id', $this->getList())
        ->getData();


        return $collection;
    }


    /**
     * Retorna os ids dos produtos da lista
     * @param unknown $id
     * @return NULL[]
     */
    public function getItemsOfListById($id){
        $productsIds = array();
        $collection = Mage::getModel('jbp_giftlist/item')
                            ->getCollection()
                            ->addFieldToSelect('*')
                            ->addFieldToFilter('jbpgi_list_id', $id);
        foreach($collection as $item){
            $productsIds[] = $item->getData('jbpgi_product_id');
        }
        return $productsIds;
    }

    /**
     * verifica se existe um produto na lista
     * @param unknown $id
     * @return unknown Retorna uma collection dos itens da lista informada
     */
    public function checkItemExists($id){
        $collection = Mage::getModel('jbp_giftlist/item')
        ->getCollection()
        ->addFieldToFilter('jbpgi_product_id', $id)
        ->addFieldToFilter('jbpgi_list_id', $this->getList())
        ->getData();
        return $collection;
    }

    /**
     * Retorna um objeto da lista de acordo com o usuário logado
     * Retorna o id da lista fechada que esta sendo visualizada dentro de uma sessão do usuário 
     */
    public function getList(){       
        
        $action = Mage::app()->getFrontController()->getAction()->getFullActionName();
        
        $param = (int)Mage::app()->getRequest()->getParam('id');        
        
        if(($action == 'giftlist_index_viewlist') && $param){
            if(!$this->isListUser($param)){                
                Mage::app()->getResponse()->setRedirect(Mage::getUrl('listaCasamento/index/minhaslistas/'));
                return;
            }
            return $param;
        }        
        
        $list = Mage::getModel('jbp_giftlist/list')
                    ->getCollection()
                    ->addFieldToFilter('jbpgl_active',array('eq'=>1))
                    ->addFieldToFilter('jbpgl_customer_id',array('eq'=>$this->getCustomer()->getId()));
        return $list->getFirstItem()->getData('jbpgl_id');        
    }

    public function getLisActive(){        
        $list = Mage::getModel('jbp_giftlist/list')
        ->getCollection()
        ->addFieldToFilter('jbpgl_active',array('eq'=>1))
        ->addFieldToFilter('jbpgl_customer_id',array('eq'=>$this->getCustomer()->getId()));
        return $list->getFirstItem()->getData('jbpgl_id');
    }
    
    /**
     * Vaida os inputs enviados do form de cadastro e edição da lista
     * @param array $post
     * @param array $exception
     * @return void|string
     */
    public function validate($post = array(), $exception = array()){
        $required = array('');
        $item = null;
        foreach($post as $k => $v){
            if(!in_array($k, $exception)){
                if(empty($v)){
                   $required[] = $k;
                }
            }
        }
        if(count($required)){
            foreach($required as $i){
                $label[] = $this->getLabel($i);
            }
            return implode('<br />Campo obrigatório: ', $label);
        }
        return;
    }

    /**
     * Pega a Action da lista do casamento editar lista
     */
    public function getAction(){
        $action = Mage::app()->getFrontController()->getAction()->getFullActionName();
        if(($action != 'giftlist_index_editarlista')){
            return false;
        }
        return true;
    }

    /**
     * Verifica se a lista do cliente existe e ainda é válida
     * @return boolean
     */
    public function checkIsValidList(){
        if($this->getCustomer()->isLoggedIn()){
           $list = Mage::getModel('jbp_giftlist/list')->load($this->getList());
           if(count($list->getData())){
               $timeCurrent = Mage::getModel('core/date')->timestamp(time());
               $timeEvent   = Mage::getModel('core/date')->timestamp($list->getData('jbpgl_event_date'));
               if($list && $list->getData('jbpgl_active') == 1){
                   if(Mage::app()->getFrontController()->getAction()->getFullActionName() == 'giftlist_index_index'){
                        #Mage::getSingleton('core/session')->addError('Você já possui uma lista criada, caso queira criar uma nova edite a existente');
                   }
                    return false;
               }
           }
        }
        return true;
    }

    /**
     * Retorna o objeto do cliente logado
     */
    public function getCustomer(){
        return Mage::getSingleton('customer/session');
    }


    /**
     * Retorna uma collection de produtos dos itens da order
     * @return unknown
     */
    public function getProductCollection(){

        $id = (int)Mage::app()->getRequest()->getParam('id');

        $storeId    = Mage::app()->getStore()->getId();
        $collection = Mage::getResourceModel('catalog/product_collection')
                            ->addMinimalPrice()
                            ->addFinalPrice()
                            ->addTaxPercents()
                            ->addUrlRewrite()
                            ->addAttributeToSelect('*')
                            ->addAttributeToFilter('status','1')
                            ->setStoreId($storeId)
                            ->addStoreFilter($storeId);

        $collection->getSelect()->join(
            'jbpgiftlist_item','(jbpgiftlist_item.jbpgi_product_id = e.entity_id) and (jbpgiftlist_item.jbpgi_list_id = '.$id.')',array('*')
            );

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        return $collection;
    }


    /**
     * Retorna o objeto do endereço da lista
     */
    public function getIdAddressListMarriageBySession(){
        $idAddress = Mage::getModel('jbp_giftlist/list')->load($this->getList())->getData('jbpgl_event_shipping_id');
        $address   = Mage::getModel('customer/address')->load($idAddress);
        return $address;
    }


    /**
     * Executa o upload da imagem de fundo
     * @param unknown $file
     * @param unknown $listId
     */
    public function uploadImage($file,$id){
       $upload = new Varien_File_Uploader($file);
       $this->validateSize($file);
       $this->isCheck($file);
       $upload->setAllowedExtensions(array('jpg','jpeg','gif','png'));
       $upload->setAllowRenameFiles(true);
       $upload->setFilesDispersion(false);
       $path = Mage::getBaseDir('media') . DS . 'listacasamento' . DS;
       $newName = $this->setNameImage($file['name'], $id);
       $this->isRemoveBannerEdit($file, $id);
       $upload->save($path, $newName);
       $nameFinal = $upload->getUploadedFileName();
       $this->cropImage($nameFinal);
       return $nameFinal;
    }


    public function isRemoveBannerEdit($file,$id){
        $listBanner = Mage::getModel('jbp_giftlist/list')->load($id)->getData('jbpgl_banner');
        if(!empty($listBanner) && !empty($file['name'])){
            $this->removeImage($listBanner);
        }
    }

    /**
     * Deleta o banner da lista
     * @param unknown $image
     */
    public function removeImage($image){
        if(!empty($image)){
            @unlink($this->getUrlImage($image));
        }
    }

    /**
     * Valida o tamanho do arquivo
     * @param unknown $size
     * @throws Exception
     */
    public function validateSize($size){
        if($size['size'] > (int)$this->getConfig()->getSizeUpload()) throw new Exception($this->__('Tamanho máximo 2MB'));
    }

    /**
     * Retorna a resolução da imagem
     * @param unknown $image
     * @return multitype:unknown array
     */
    public function getImageResolution($image){
        $info = getimagesize($image['tmp_name']);
        $res = array(
            'width' =>$info[0],
            'height' =>$info[1],
        );

        return $res;
    }

    /**
     * Redimensiona a imagem
     * @param unknown $image
     */
    public function cropImage($image){
        $image = $this->getUrlImage($image);
        $imageObj = new Varien_Image($image);
        $imageObj->resize($this->getConfig()->getWidthImage(), $this->getConfig()->getHeigthImage());
        $imageObj->save($image);
    }

    /**
     * Valida a resolução da imagem
     * @param unknown $image
     * @throws Exception
     */
    public function isCheck($image){
        $image = $this->getImageResolution($image);
        if($image['width']  < $this->getConfig()->getWidthImage() && $image['heith'] < $this->getConfig()->getHeigthImage()) throw new Exception($this->__('Envie uma imagem com resolução acima de %s x %s',$this->getConfig()->getWidthImage(),$this->getConfig()->getHeigthImage()));
    }


    /*
     * Retorna o caminho da imagem do banner
     */
    public function getUrlImage($img){
        $img = Mage::getBaseDir('media') . DS . 'listacasamento' . DS .$img;
        if(@file_exists($img)){
            return $img;
        }
    }

    /**
     * Formata a data
     * @param unknown $data
     * @return string
     */
    public function formatDate($data){
        return implode('-', array_reverse(explode('/', $data)));
    }

    /*
     * Retorna a URL da imagem do banner caminho interno
     */
    public function getPathImage($img){
        $imgPath = Mage::getBaseDir('media') . DS . 'listacasamento' . DS .$img;
        if(@file_exists($imgPath)){
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/listacasamento/'.$img;
        }
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/listacasamento/'.$this->getConfig()->getImageDefault();
    }

    /**
     * Renomeia o nome do banner
     * @param unknown $name
     * @param unknown $id
     * @return string
     */
    public function setNameImage($name,$id){
        $ext = end(explode('.', $name));
        $name_e = explode('.', $name);
        $name = $name_e[0].'_'.md5($name.time().mt_rand(0, time())).'_'.$id.'.'.$ext;
        return $name;
    }

    /**
     * Executa o envio de e-mail geral
     * @param unknown $data
     */
    public function sendEmailGeneral($data){
        $mail = Mage::getModel('core/email_template')->loadDefault($data['template_id']);
        $processedTemplate = $mail->getProcessedTemplate($data);
        $mail->setSenderName($data['subject']);
        $mail->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email'));
        $mail->setTemplateSubject($data['subject']);
        try{
            $mail->send($data['mail_destinatario'], $data['subject'], $data);
        }catch(Exception $e){
            Mage::log($data['log_error']. ' Error: '.$e->getMessage());
        }
    }

    /**
     * Retorna um objeto da lista de casamento
     * @param unknown $id
     */
    public function getListById($id){
        $list = Mage::getModel('jbp_giftlist/list')->load($id);
        return $list;
    }

    /**
     * Valida se o botão pode ser visualizado
     * @return boolean
     */
    public function isShowButton(){
        if($this->getCustomer()->isLoggedIn() && $this->getList() )return true;
        return;
    }

    /**
     * Verifica se a lista consultada pertence ao usuário atual
     * @param unknown $id
     * @return boolean
     */
    public function isListUser($id){
        $list = Mage::getModel('jbp_giftlist/list')->load((int)$id);        
        if($list->getData('jbpgl_customer_id') != $this->getCustomer()->getId()) return false;
        return true;        
    }
    
    /**
     * Verifica se o cupom já foi usado em algum pedido
     * @param unknown $coupon
     * @return unknown
     */
    public function isValidCoupon($coupon){
        $collection = Mage::getResourceModel('sales/order_collection');
        $collection->addAttributeToFilter('coupon_code', array('eq'=>$coupon));
        return $collection->getSize();                           
    }
    
    /**
     * Retorna um objeto da helper de configuração do módulo
     */
    public function getConfig(){
        return Mage::helper('jbp_giftlist/config');
    }



















}
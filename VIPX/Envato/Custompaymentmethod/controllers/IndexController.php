<?php
class Envato_Custompaymentmethod_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction(){
        echo $img = '<img src="'.Mage::getDesign()->getSkinUrl('/envato/custompaymentmethod/image/RAM.png').'" />';
    }


    public function testeAction(){

        $data = Mage::getModel('magecomp_deliverydate/quote')->load(190, 'quote_id')->getData();


        var_dump($data);



    }


}
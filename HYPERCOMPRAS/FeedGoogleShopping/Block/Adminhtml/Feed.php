<?php
class HC_FeedGoogleShopping_Block_Adminhtml_Feed extends Mage_Adminhtml_Block_Template {


    public function getCatGoogleShopping(){
        return Mage::getModel('hc_feedgoogleshopping/feed')->getCategoryGoogleShopping();
    }

}
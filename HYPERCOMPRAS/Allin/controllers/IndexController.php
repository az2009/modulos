<?php
class HC_Allin_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction(){
/*
        $setup = new Mage_Eav_Model_Entity_Setup('core_setup');    
        $setup->startSetup();
        $setup->removeAttribute('customer', 'middlename');
        $setup->endSetup();
 */
	echo "<pre>";
/*
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
        
        $entityTypeId     = $setup->getEntityTypeId('customer');
        $attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
        $attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);
        
        $setup->addAttribute('customer', 'middlename', array(
            'input'         => 'text',
            'type'          => 'text',
            'label'         => 'middlename',
            'visible'       => 1,
            'required'      => 1,
            'user_defined'  => 1,
        ));
        
        $setup->addAttributeToGroup(
            $entityTypeId,
            $attributeSetId,
            $attributeGroupId,
            'middlename',
            '999'  //sort_order
            );
        
        
        $oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'middlename');
        $oAttribute->setData('used_in_forms', array('adminhtml_customer'));
        
        $oAttribute->save();
*/
        return;
    }

}

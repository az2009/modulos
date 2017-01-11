<?php
$installer = $this;
$installer->startSetup();

$attribute  = array(
    'type'          =>  'text',
    'label'         =>  'Google Shopping',
    'input'         =>  'text',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "General"
);
$installer->addAttribute('catalog_product', 'feed_google_shopping', $attribute);

$installer->endSetup();
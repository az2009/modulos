<?php
$installer = $this;
$installer->startSetup();

    $installer->getConnection()->addColumn(
        $installer->getTable('catalog/product_option_type_value'), 'details', 'VARCHAR(128) NULL');

    $installer->getConnection()->addColumn(
        $installer->getTable('catalog/product_option_type_value'), 'modo', 'VARCHAR(128) NULL');

$installer->endSetup();
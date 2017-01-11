<?php

$installer = $this;

$installer->startSetup();

$installer->run("

    DROP TABLE IF EXISTS {$this->getTable('jbpgiftlist_item')};
    CREATE TABLE {$this->getTable('jbpgiftlist_item')}(
        jbpgi_id INT NOT NULL AUTO_INCREMENT,
        jbpgi_product_id INT NOT NULL,
        jbpgi_list_id INT NOT NULL,
        jbpgi_resgatar INT NOT NULL,
        PRIMARY KEY (jbpgi_id),
        FOREIGN KEY (jbpgi_list_id) REFERENCES {$this->getTable('jbpgiftlist_list')}(jbpgl_id)
    )ENGINE=InnoDB DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS {$this->getTable('jbpgiftlist_address_event')};
    CREATE TABLE {$this->getTable('jbpgiftlist_address_event')} (
        jbpgae_id INT NOT NULL AUTO_INCREMENT,
        jbpgae_cidade VARCHAR(255),
        jbpgae_estado VARCHAR(255),
        PRIMARY KEY (jbpgae_id)
    )ENGINE=InnoDB DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS {$this->getTable('jbpgiftlist_list')};
    CREATE TABLE {$this->getTable('jbpgiftlist_list')} (
        jbpgl_id INT NOT NULL AUTO_INCREMENT,
        jbpgl_name_noivo VARCHAR(500) NOT NULL default '',
        jbpgl_name_noiva VARCHAR(500) NOT NULL default '',
        jbpgl_customer_id INT NOT NULL,
        jbpgl_description VARCHAR(800) NOT NULL default '',
        jbpgl_store_id INT NOT NULL,
        jbpgl_created_at DATETIME NOT NULL,
        jbpgl_banner VARCHAR(800) NOT NULL,
        jbpgl_event_name VARCHAR(500) NOT NULL,
        jbpgl_event_date DATETIME NOT NULL,
        jbpgl_active CHAR(1) DEFAULT 1,
        jbpgl_event_shipping_id INT NOT NULL,
        jbpgl_event_address_id INT NOT NULL,
        jbpgl_item_id INT NOT NULL,
        PRIMARY KEY (jbpgl_id),
        FOREIGN KEY (jbpgl_event_address_id) REFERENCES {$this->getTable('jbpgiftlist_address_event')}(jbpgae_id)
    )ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addColumn($this->getTable('sales/order'), 'jbp_giftlist_id', 'int(11) NOT NULL default 0');
$installer->endSetup();

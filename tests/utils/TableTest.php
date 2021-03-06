<?php

namespace SqlParser\Tests\Utils;

use SqlParser\Parser;
use SqlParser\Utils\Table;

use SqlParser\Tests\TestCase;

class TableTest extends TestCase
{

    /**
     * @dataProvider getForeignKeysProvider
     */
    public function testGetForeignKeys($query, array $expected)
    {
        $parser = new Parser($query);
        $this->assertEquals($expected, Table::getForeignKeys($parser->statements[0]));
    }

    public function getForeignKeysProvider()
    {
        return array(
            array(
                'CREATE USER test',
                array(),
            ),
            array(
                'CREATE TABLE `payment` (
                  `payment_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
                  `customer_id` smallint(5) unsigned NOT NULL,
                  `staff_id` tinyint(3) unsigned NOT NULL,
                  `rental_id` int(11) DEFAULT NULL,
                  `amount` decimal(5,2) NOT NULL,
                  `payment_date` datetime NOT NULL,
                  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`payment_id`),
                  KEY `idx_fk_staff_id` (`staff_id`),
                  KEY `idx_fk_customer_id` (`customer_id`),
                  KEY `fk_payment_rental` (`rental_id`),
                  CONSTRAINT `fk_payment_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON UPDATE CASCADE,
                  CONSTRAINT `fk_payment_rental` FOREIGN KEY (`rental_id`) REFERENCES `rental` (`rental_id`) ON DELETE SET NULL ON UPDATE CASCADE,
                  CONSTRAINT `fk_payment_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON UPDATE CASCADE
                ) ENGINE=InnoDB AUTO_INCREMENT=16050 DEFAULT CHARSET=utf8',
                array(
                    array(
                        'constraint' => 'fk_payment_customer',
                        'index_list' => array('customer_id'),
                        'ref_table_name' => 'customer',
                        'ref_index_list' => array('customer_id'),
                        'on_update' => 'CASCADE',
                    ),
                    array(
                        'constraint' => 'fk_payment_rental',
                        'index_list' => array('rental_id'),
                        'ref_table_name' => 'rental',
                        'ref_index_list' => array('rental_id'),
                        'on_delete' => 'SET_NULL',
                        'on_update' => 'CASCADE',
                    ),
                    array(
                        'constraint' => 'fk_payment_staff',
                        'index_list' => array('staff_id'),
                        'ref_table_name' => 'staff',
                        'ref_index_list' => array('staff_id'),
                        'on_update' => 'CASCADE',
                    ),
                ),
            ),
            array(
                'CREATE TABLE `actor` (
                  `actor_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
                  `first_name` varchar(45) NOT NULL,
                  `last_name` varchar(45) NOT NULL,
                  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`actor_id`),
                  KEY `idx_actor_last_name` (`last_name`)
                ) ENGINE=InnoDB AUTO_INCREMENT=201 DEFAULT CHARSET=utf8',
                array(),
            ),
            array(
                'CREATE TABLE `address` (
                  `address_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
                  `address` varchar(50) NOT NULL,
                  `address2` varchar(50) DEFAULT NULL,
                  `district` varchar(20) NOT NULL,
                  `city_id` smallint(5) unsigned NOT NULL,
                  `postal_code` varchar(10) DEFAULT NULL,
                  `phone` varchar(20) NOT NULL,
                  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`address_id`),
                  KEY `idx_fk_city_id` (`city_id`),
                  CONSTRAINT `fk_address_city` FOREIGN KEY (`city_id`) REFERENCES `city` (`city_id`) ON UPDATE CASCADE
                ) ENGINE=InnoDB AUTO_INCREMENT=606 DEFAULT CHARSET=utf8',
                array(
                    array(
                        'constraint' => 'fk_address_city',
                        'index_list' => array('city_id'),
                        'ref_table_name' => 'city',
                        'ref_index_list' => array('city_id'),
                        'on_update' => 'CASCADE',
                    ),
                ),
            ),
        );
    }

    /**
     * @dataProvider getFieldsProvider
     */
    public function testGetFields($query, array $expected)
    {
        $parser = new Parser($query);
        $this->assertEquals($expected, Table::getFields($parser->statements[0]));
    }

    public function getFieldsProvider()
    {
        return array(
            array(
                'CREATE USER test',
                array(),
            ),
            array(
                'CREATE TABLE `address` (
                  `address_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
                  `address` varchar(50) NOT NULL,
                  `address2` varchar(50) DEFAULT NULL,
                  `district` varchar(20) NOT NULL,
                  `city_id` smallint(5) unsigned NOT NULL,
                  `postal_code` varchar(10) DEFAULT NULL,
                  `phone` varchar(20) NOT NULL,
                  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`address_id`),
                  KEY `idx_fk_city_id` (`city_id`),
                  CONSTRAINT `fk_address_city` FOREIGN KEY (`city_id`) REFERENCES `city` (`city_id`) ON UPDATE CASCADE
                ) ENGINE=InnoDB AUTO_INCREMENT=606 DEFAULT CHARSET=utf8',
                array(
                    'address_id' => array(
                        'type' => 'SMALLINT',
                        'timestamp_not_null' => null,
                    ),
                    'address' => array(
                        'type' => 'VARCHAR',
                        'timestamp_not_null' => null,
                    ),
                    'address2' => array(
                        'type' => 'VARCHAR',
                        'timestamp_not_null' => null,
                        'default_value' => 'NULL',
                    ),
                    'district' => array(
                        'type' => 'VARCHAR',
                        'timestamp_not_null' => null,
                    ),
                    'city_id' => array(
                        'type' => 'SMALLINT',
                        'timestamp_not_null' => null,
                    ),
                    'postal_code' => array(
                        'type' => 'VARCHAR',
                        'timestamp_not_null' => null,
                        'default_value' => 'NULL',
                    ),
                    'phone' => array(
                        'type' => 'VARCHAR',
                        'timestamp_not_null' => null,
                    ),
                    'last_update' => array(
                        'type' => 'TIMESTAMP',
                        'timestamp_not_null' => true,
                        'default_value' => 'CURRENT_TIMESTAMP',
                        'default_current_timestamp' => true,
                        'on_update_current_timestamp' => true
                    )
                )
            ),
        );
    }
}

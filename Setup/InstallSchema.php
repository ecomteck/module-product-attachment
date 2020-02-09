<?php

/**
 * Ecomteck
 * Copyright (C) 2018 Ecomteck
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html
 *
 * @category Ecomteck
 * @package Ecomteck_ProductAttachment
 * @copyright Copyright (c) 2018 Ecomteck
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Ecomteck
 */

namespace Ecomteck\ProductAttachment\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Ecomteck\ProductAttachment\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /*
         * Drop tables if exists
         */

        $installer->getConnection()->dropTable($installer->getTable('ecomteck_productattachment'));
        $installer->getConnection()->dropTable($installer->getTable('ecomteck_productattachment_fileicon'));

        /**
         * Creating table ecomteck_productattachment
         */
        $table_ecomteck_productattachment = $installer->getConnection()->newTable($installer->getTable('ecomteck_productattachment'));
        
        $table_ecomteck_productattachment->addColumn(
            'productattach_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        );

        $table_ecomteck_productattachment->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Name'
        );

        $table_ecomteck_productattachment->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true,'default' => null],
            'Description'
        );

        $table_ecomteck_productattachment->addColumn(
            'file',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true,'default' => null],
            'Content'
        );

        $table_ecomteck_productattachment->addColumn(
            'file_ext',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true,'default' => null],
            'File Extension'
        );

        $table_ecomteck_productattachment->addColumn(
            'url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true,'default' => null],
            'ProductAttachment image media path'
        );

        $table_ecomteck_productattachment->addColumn(
            'store',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true,'default' => null],
            'Store'
        );

        $table_ecomteck_productattachment->addColumn(
            'customer_group',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true,'default' => null],
            'Customer Group'
        );

        $table_ecomteck_productattachment->addColumn(
            'products',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true,'default' => null],
            'Assigned Products'
        );

        $table_ecomteck_productattachment->addColumn(
            'active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Active'
        );

        $table_ecomteck_productattachment->addColumn(
            'visible_scope',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            1,
            ['nullable' => false, 'default' => '0'],
            'Visible to people who have not purchased this item'
        );

        $table_ecomteck_productattachment->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false],
            'Created At'
        );

        $table_ecomteck_productattachment->addColumn(
            'published_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => true,'default' => null],
            'World publish date'
        );

        $table_ecomteck_productattachment->addIndex(
            $installer->getIdxName(
                'Prince_productattach',
                ['published_at'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
            ),
            ['published_at'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX]
        )->setComment('Product Attachment item');

        $installer->getConnection()->createTable($table_ecomteck_productattachment);

        $table_ecomteck_productattachment_fileicon = $setup->getConnection()->newTable($setup->getTable('ecomteck_productattachment_fileicon'));
        
        $table_ecomteck_productattachment_fileicon->addColumn(
            'fileicon_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        
        $table_ecomteck_productattachment_fileicon->addColumn(
            'icon_ext',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'icon_ext'
        );
        
        $table_ecomteck_productattachment_fileicon->addColumn(
            'icon_image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'icon_image'
        );

        $setup->getConnection()->createTable($table_ecomteck_productattachment_fileicon);

        $installer->endSetup();
    }
}

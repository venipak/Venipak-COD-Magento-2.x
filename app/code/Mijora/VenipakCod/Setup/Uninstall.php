<?php

namespace Mijora\VenipakCod\Setup;
 
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
 
class Uninstall implements UninstallInterface
{
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
 
        $setup->getConnection()->dropColumn($setup->getTable('quote'), 'venipak_cod_fee');
        $setup->getConnection()->dropColumn($setup->getTable('sales_order'), 'venipak_cod_fee');
 
        
        $setup->endSetup();
    }
}
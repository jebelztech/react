<?php

namespace Satish\Manage\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.0') < 0){

		$installer->run('CREATE TABLE `manage_forms` (
  `id` int(100) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `subject` text DEFAULT NULL,
  `status` varchar(10) DEFAULT \'Pending\',
  `page_type` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `company_name` varchar(200) DEFAULT NULL,
  `ip` varchar(111) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1');
$installer->run('--
-- Indexes for dumped tables
--

--
-- Indexes for table `manage_forms`
--
ALTER TABLE `manage_forms`
  ADD PRIMARY KEY (`id`)');
$installer->run('--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `manage_forms`
--
ALTER TABLE `manage_forms`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT');
$installer->run('COMMIT');
$installer->run('--
-- Table structure for table `manage_images`
--

CREATE TABLE `manage_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(222) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_mobile` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sorting` int(111) DEFAULT NULL,
  `type` varchar(111) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
$installer->run('--
-- Indexes for dumped tables
--

--
-- Indexes for table `manage_images`
--
ALTER TABLE `manage_images`
  ADD PRIMARY KEY (`id`)');
$installer->run('--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `manage_images`
--
ALTER TABLE `manage_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT');
$installer->run('COMMIT');


		//demo
//$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//$scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
//$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/updaterates.log');
//$logger = new \Zend\Log\Logger();
//$logger->addWriter($writer);
//$logger->info('updaterates');
//demo 

		}

        $installer->endSetup();

    }
}
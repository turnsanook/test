<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_RulesPro
 */


namespace Amasty\RulesPro\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Amasty\RulesPro\Setup\Operation\AddRuleUsage;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var AddRuleUsage
     */
    private $addRuleUsage;

    public function __construct(
        AddRuleUsage $addRuleUsage
    ) {
        $this->addRuleUsage = $addRuleUsage;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addRuleUsage->execute($setup);
        }

        $setup->endSetup();
    }
}

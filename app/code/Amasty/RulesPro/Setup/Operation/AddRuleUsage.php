<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_RulesPro
 */


namespace Amasty\RulesPro\Setup\Operation;

use Amasty\RulesPro\Model\ResourceModel\RuleUsageCounter;
use Amasty\RulesPro\Model\ResourceModel\RuleUsageLimit;
use Amasty\RulesPro\Api\RuleUsageRepositoryInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\SalesRule\Api\Data\RuleInterface;

class AddRuleUsage
{
    /**
     * @var MetadataPool
     */
    private $metadata;

    public function __construct(
        MetadataPool $metadata
    ) {
        $this->metadata = $metadata;
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->createRuleUsageLimitTable($setup);
        $this->createRuleUsageCounterTable($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function createRuleUsageLimitTable(SchemaSetupInterface $setup)
    {
        $salesruleLinkField = $this->metadata->getMetadata(RuleInterface::class)
            ->getLinkField();
        $metadata = $this->metadata->getMetadata(RuleInterface::class);
        /**
         * Create table 'amasty_amrules_usage_limit'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable(RuleUsageLimit::TABLE_NAME))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'salesrule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )->addColumn(
                RuleUsageRepositoryInterface::LIMIT_USAGE_COLUMN,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0]
            )->addIndex(
                $setup->getIdxName(RuleUsageLimit::TABLE_NAME, ['salesrule_id']),
                ['salesrule_id']
            )->addForeignKey(
                $setup->getFkName(
                    RuleUsageLimit::TABLE_NAME,
                    'salesrule_id',
                    'salesrule',
                    $salesruleLinkField
                ),
                'salesrule_id',
                $metadata->getEntityTable(),
                $salesruleLinkField,
                Table::ACTION_CASCADE
            );

        $setup->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function createRuleUsageCounterTable(SchemaSetupInterface $setup)
    {
        $salesruleLinkField = $this->metadata->getMetadata(RuleInterface::class)
            ->getLinkField();
        /**
         * Create table 'amasty_amrules_usage_counter'
         */
        $metadata = $this->metadata->getMetadata(RuleInterface::class);
        $table = $setup->getConnection()
            ->newTable($setup->getTable(RuleUsageCounter::TABLE_NAME))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )->addColumn(
                'salesrule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true]
            )->addColumn(
                RuleUsageRepositoryInterface::COUNT_USAGE_COLUMN,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0]
            )->addIndex(
                $setup->getIdxName(RuleUsageCounter::TABLE_NAME, ['salesrule_id']),
                ['salesrule_id']
            )->addForeignKey(
                $setup->getFkName(
                    RuleUsageCounter::TABLE_NAME,
                    'salesrule_id',
                    'salesrule',
                    'rule_id'
                ),
                'salesrule_id',
                $metadata->getEntityTable(),
                $metadata->getIdentifierField(),
                Table::ACTION_CASCADE
            );

        $setup->getConnection()->createTable($table);
    }
}

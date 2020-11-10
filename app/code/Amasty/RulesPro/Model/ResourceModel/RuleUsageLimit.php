<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_RulesPro
 */


namespace Amasty\RulesPro\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Amasty\RulesPro\Api\RuleUsageRepositoryInterface;

class RuleUsageLimit extends AbstractDb
{
    const TABLE_NAME = 'amasty_amrules_usage_limit';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'entity_id');
    }

    /**
     * @param int $salesRuleId
     *
     * @return int
     */
    public function getLimitByRuleId($salesRuleId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable(self::TABLE_NAME),
            [RuleUsageRepositoryInterface::LIMIT_USAGE_COLUMN]
        )->where(
            'salesrule_id = ?',
            $salesRuleId
        );

        return (int)$connection->fetchOne($select);
    }

    /**
     * @param int $salesRuleId
     * @param int $limit
     */
    public function saveUsageLimit(int $salesRuleId, int $limit = 0)
    {
        $connection = $this->getConnection();
        $rowCount = $connection->fetchOne(
            $connection->select()->from($this->getTable(self::TABLE_NAME), [new \Zend_Db_Expr('COUNT(*)')])->where(
                'salesrule_id = ?',
                $salesRuleId
            )
        );
        if ($rowCount > 0) {
            $connection->update(
                $this->getTable(self::TABLE_NAME),
                [RuleUsageRepositoryInterface::LIMIT_USAGE_COLUMN => $limit],
                ['salesrule_id = ?' => $salesRuleId]
            );
        } else {
            $connection->insert(
                $this->getTable(self::TABLE_NAME),
                ['salesrule_id' => $salesRuleId, RuleUsageRepositoryInterface::LIMIT_USAGE_COLUMN => $limit]
            );
        }
    }
}

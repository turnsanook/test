<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_RulesPro
 */


namespace Amasty\RulesPro\Api;

interface RuleUsageRepositoryInterface
{
    const COUNT_USAGE_COLUMN = 'count';

    const LIMIT_USAGE_COLUMN = 'limit';

    /**
     * @param int $salesRuleId
     *
     * @return int
     */
    public function getUsageLimitByRuleId(int $salesRuleId);

    /**
     * @param int $salesRuleId
     *
     * @return int
     */
    public function getUsageCountByRuleId(int $salesRuleId);

    /**
     * @param int $salesRuleId
     * @param int $limit
     */
    public function saveUsageLimit(int $salesRuleId, int $limit);

    /**
     * @param int $salesRuleId
     * @param int $count
     */
    public function saveUsageCount(int $salesRuleId, int $count);

    /**
     * @param array $ruleIds
     */
    public function incrementUsageCountByRuleIds(array $ruleIds);
}

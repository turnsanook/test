<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_RulesPro
 */


namespace Amasty\RulesPro\Model;

use Amasty\RulesPro\Api\RuleUsageRepositoryInterface;
use Amasty\RulesPro\Model\ResourceModel\RuleUsageCounter;
use Amasty\RulesPro\Model\ResourceModel\RuleUsageLimit;

class RuleUsageRepository implements RuleUsageRepositoryInterface
{
    /**
     * @var RuleUsageLimit
     */
    private $ruleUsageLimitResource;

    /**
     * @var RuleUsageCounter
     */
    private $ruleUsageCounterResource;

    public function __construct(
        RuleUsageLimit $ruleUsageLimitResource,
        RuleUsageCounter $ruleUsageCounterResource
    ) {
        $this->ruleUsageCounterResource = $ruleUsageCounterResource;
        $this->ruleUsageLimitResource = $ruleUsageLimitResource;
    }

    /**
     * @param int $salesRuleId
     *
     * @return int
     */
    public function getUsageLimitByRuleId(int $salesRuleId)
    {
        return $this->ruleUsageLimitResource->getLimitByRuleId($salesRuleId);
    }

    /**
     * @param int $salesRuleId
     *
     * @return int
     */
    public function getUsageCountByRuleId(int $salesRuleId)
    {
        return $this->ruleUsageCounterResource->getCountByRuleId($salesRuleId);
    }

    /**
     * @param int $salesRuleId
     * @param int $limit
     */
    public function saveUsageLimit(int $salesRuleId, int $limit)
    {
        $this->ruleUsageLimitResource->saveUsageLimit($salesRuleId, $limit);
    }

    /**
     * @param int $salesRuleId
     * @param int $count
     */
    public function saveUsageCount(int $salesRuleId, int $count)
    {
        $this->ruleUsageCounterResource->saveUsageCount($salesRuleId, $count);
    }

    /**
     * @param array $ruleIds
     */
    public function incrementUsageCountByRuleIds(array $ruleIds)
    {
        $this->ruleUsageCounterResource->incrementUsageCountByRuleIds($ruleIds);
    }
}

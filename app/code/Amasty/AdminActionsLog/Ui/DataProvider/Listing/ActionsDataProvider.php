<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


declare(strict_types=1);

namespace Amasty\AdminActionsLog\Ui\DataProvider\Listing;

use Amasty\AdminActionsLog\Model\ResourceModel\Log\CollectionFactory;
use Magento\Framework\DB\Select;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Api\Filter;

class ActionsDataProvider extends AbstractDataProvider
{
    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    public function __construct(
        CollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create()->joinAdminDataTable();
        $this->addFilterStrategies = $addFilterStrategies;
    }

    public function addFilter(Filter $filter)
    {
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
        } else {
            parent::addFilter($filter);
        }
    }
}

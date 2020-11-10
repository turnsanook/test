<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Model\ResourceModel\Log;

use Amasty\AdminActionsLog\Ui\Component\Listing\Filter\AddFullnameFilterToCollection;
use Magento\Framework\DB\Select;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Amasty\AdminActionsLog\Model\Log::class,
            \Amasty\AdminActionsLog\Model\ResourceModel\Log::class
        );
    }

    public function joinAdminDataTable()
    {
        $fromPart = $this->getSelect()->getPart(Select::FROM);

        if (!isset($fromPart[AddFullnameFilterToCollection::TABLE_ALIAS])) {
            $this->getSelect()->joinLeft(
                [AddFullnameFilterToCollection::TABLE_ALIAS => $this->getTable('admin_user')],
                'main_table.username = ' . AddFullnameFilterToCollection::TABLE_ALIAS . '.username',
                ['fullname' => AddFullnameFilterToCollection::SQL_EXPRESSION, 'firstname', 'lastname']
            );
        }

        return $this;
    }
}

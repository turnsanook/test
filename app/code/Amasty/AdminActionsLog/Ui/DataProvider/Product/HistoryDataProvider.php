<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


declare(strict_types=1);

namespace Amasty\AdminActionsLog\Ui\DataProvider\Product;

use Amasty\AdminActionsLog\Model\ResourceModel\Log\CollectionFactory;
use Amasty\AdminActionsLog\Model\ResourceModel\LogDetails\Collection as DetailsCollection;
use Amasty\AdminActionsLog\Ui\DataProvider\Listing\ActionsDataProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DB\Select;

class HistoryDataProvider extends ActionsDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var DetailsCollection
     */
    protected $detailsCollection;

    public function __construct(
        CollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        RequestInterface $request,
        DetailsCollection $detailsCollection,
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $collectionFactory,
            $name,
            $primaryFieldName,
            $requestFieldName,
            $addFilterStrategies,
            $meta,
            $data
        );

        $this->request = $request;
        $this->detailsCollection = $detailsCollection;
    }

    public function getData(): array
    {
        $elementId = $this->request->getParam('current_product_id');
        $this->collection->getSelect()
            ->joinLeft(
                [
                    'r' => $this->detailsCollection->getMainTable()
                ],
                'main_table.id = r.log_id',
                [
                    'is_logged' => 'MAX(r.log_id)'
                ]
            )
            ->where("element_id = ?", $elementId)
            ->where("category = ?", 'catalog/product')
            ->group('r.log_id');

        $arrItems = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => [],
        ];

        foreach ($this->getCollection() as $item) {
            $arrItems['items'][] = $item->toArray([]);
        }

        return $arrItems;
    }
}

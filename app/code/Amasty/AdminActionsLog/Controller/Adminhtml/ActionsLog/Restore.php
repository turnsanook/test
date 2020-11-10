<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Controller\Adminhtml\ActionsLog;

class Restore extends \Magento\Backend\App\Action
{
    protected $_helper;
    protected $_scopeConfig;
    protected $_resourceConfig;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_resourceConfig = $resourceConfig;
        parent::__construct($context);
    }

    public function execute()
    {
        $logId = $this->getRequest()->getParam('log_id');
        /** @var \Amasty\AdminActionsLog\Model\Log $logModel */
        $logModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\Log')->load($logId);

        /** @var \Amasty\AdminActionsLog\Model\LogDetails $logDetailsModel */
        $logDetailsModel =  $this->_objectManager->get('Amasty\AdminActionsLog\Model\LogDetails');
        $logDetailsCollection = $logDetailsModel->getCollection();
        $logDetailsCollection->addFieldToFilter('log_id', array('in' => $logId));

        $elementId = $logModel->getElementId();
        $elementLoaded = false;
        $logLoaded = false;
        $skipQtyAndStock = false;

        foreach ($logDetailsCollection as $logDetail) {
            $elementKey = $logDetail->getName();
            $oldValue = $logDetail->getOldValue();
            $modelName = $logDetail->getModel();

            if ($logModel->getCategory() == 'admin/system_config') {
                if (!$logLoaded) {
                    /** @var \Amasty\AdminActionsLog\Model\Log $newLogModel */
                    $newLogModel = $this->_objectManager->create('Amasty\AdminActionsLog\Model\Log');
                    $data = $logModel->getData();
                    $data['type'] = 'Restore';
                    $data['date_time'] = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate();
                    unset($data['id']);
                    $newLogModel->setData($data);
                    $newLogModel->save();
                    $logLoaded = true;
                }

                /** @var \Amasty\AdminActionsLog\Model\LogDetails $newLogDetailsModel */
                $newLogDetailsModel = $this->_objectManager->create('Amasty\AdminActionsLog\Model\LogDetails');
                $dataNewDetails = $logDetail->getData();
                unset($dataNewDetails['id']);
                $dataNewDetails['new_value'] = $oldValue;
                $dataNewDetails['old_value'] = $this->_scopeConfig->getValue($logDetail->getName());
                $dataNewDetails['log_id'] = $newLogModel->getId();
                $newLogDetailsModel->setData($dataNewDetails);
                $newLogDetailsModel->save();

                $this->_resourceConfig->saveConfig(
                    $elementKey,
                    $oldValue,
                    'default',
                    $logModel->getStoreId()
                );
            } else {
                if (!$elementLoaded) {
                    $element = $this->_objectManager->get($modelName)->load($elementId);
                    $elementLoaded = true;
                }

                if (($elementKey === 'is_in_stock' || $elementKey === 'qty')
                    && $element->getData($elementKey) === null
                ) {
                    if ($skipQtyAndStock) {
                        continue;
                    }
                    list($elementKey, $oldValue) = $this->restoreStockStatus(
                        $element,
                        $logDetailsModel,
                        $logDetail,
                        $elementKey,
                        $oldValue,
                        $skipQtyAndStock
                    );
                }
                $element->setData($elementKey, $oldValue);

                if (!$element->hasData('store_id')) {
                    if ($logModel->hasData('store_id')) {
                        $element->setData('store_id', $logModel->getData('store_id'));
                    } else {
                        $element->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
                    }
                }

                $element->save();
            }

        }

        $this->_redirect('amaudit/actionslog/index');
    }

    /**
     * @param object $element
     * @param \Amasty\AdminActionsLog\Model\LogDetails $logDetailsModel
     * @param \Amasty\AdminActionsLog\Model\LogDetails $logDetail
     * @param string $elementKey
     * @param string $oldValue
     * @param bool $skipQtyAndStock
     *
     * @return array
     */
    private function restoreStockStatus(
        $element,
        $logDetailsModel,
        $logDetail,
        $elementKey,
        $oldValue,
        &$skipQtyAndStock
    ) {
        $stockDataKey = 'quantity_and_stock_status';
        $inStockLogItem = $elementKey === 'qty'
            ? $logDetailsModel->getCollection()
                ->addFieldToFilter('log_id', $logDetail->getData('log_id'))
                ->addFieldToFilter('name', 'is_in_stock')
                ->getFirstItem()
                ->getData()
            : null;
        $stockData = $element->getData($stockDataKey);

        if ($inStockLogItem) {
            $stockData['is_in_stock'] = $inStockLogItem['old_value'];
            $skipQtyAndStock = true;
        }
        $stockData[$elementKey] = $oldValue;

        return [$stockDataKey, $stockData];
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_AdminActionsLog::actions_log');
    }
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


namespace Amasty\Rules\Plugin\Framework\Api;

use Amasty\Rules\Plugin\Cart\CartTotalRepository as CartTotalRepositoryPlugin;
use Magento\Framework\Api\ExtensibleDataInterface;
use Amasty\Rules\Model\Registry;
use Magento\Quote\Api\Data\TotalsInterface;

/**
 * resolve fatal
 * @see \Amasty\Rules\Plugin\Cart\CartTotalRepository::beforeGet
 */
class DataObjectHelperPlugin
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Framework\Api\DataObjectHelper $subject
     * @param object $dataObject
     * @param array $data
     * @param string $interfaceName
     *
     * @return array
     */
    public function beforePopulateWithArray(
        \Magento\Framework\Api\DataObjectHelper $subject,
        $dataObject,
        array $data,
        $interfaceName
    ) {
        if ($interfaceName === TotalsInterface::class
            && $this->registry->registry(CartTotalRepositoryPlugin::REGISTRY_IGNORE_EXTENSION_ATTRIBUTES_KEY)
        ) {
            unset($data[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);
        }

        return [$dataObject, $data, $interfaceName];
    }
}

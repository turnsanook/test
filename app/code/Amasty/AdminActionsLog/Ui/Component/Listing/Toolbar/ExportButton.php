<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


declare(strict_types=1);

namespace Amasty\AdminActionsLog\Ui\Component\Listing\Toolbar;

use Magento\Ui\Component\ExportButton as MagentoExportButton;

class ExportButton extends MagentoExportButton
{
    public function prepare(): void
    {
        // Fixed exportButton option cvs (typo csv) for Magento less than 2.3.3
        if ($this->getData('config/options/cvs')) {
            $config = $this->getData('config');
            unset($config['options']['cvs']);
            $this->setData('config', $config);
        }

        parent::prepare();
    }
}

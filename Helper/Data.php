<?php
/*
 * *
 *  * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */

namespace Udeytech\DeleteOrder\Helper;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Config\ConfigOptionsListConstants;

/**
 * Class Data
 * @package Udeytech\DeleteOrder\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var DeploymentConfig
     */
    protected $deploymentConfig;

    /**
     * Data constructor.
     * @param Context $context
     * @param DeploymentConfig $deploymentConfig
     */
    public function __construct(
        Context $context,
        DeploymentConfig $deploymentConfig
    )
    {
        parent::__construct($context);
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * @param null $name
     * @return false|mixed|string
     */
    public function getTableName($name = null)
    {
        if ($name == null) {
            return false;
        }
        $tableName = $name;
        $tablePrefix = (string)$this->deploymentConfig->get(
            ConfigOptionsListConstants::CONFIG_PATH_DB_PREFIX
        );
        if ($tablePrefix) {
            $tableName = $tablePrefix . $name;
        }
        return $tableName;
    }
}

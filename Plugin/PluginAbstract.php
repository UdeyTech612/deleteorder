<?php
/*
 * *
 *  * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */

namespace Udeytech\DeleteOrder\Plugin;

use Magento\Authorization\Model\Acl\AclRetriever;
use Magento\Backend\Model\Auth\Session;

/**
 * Class PluginAbstract
 * @package Udeytech\DeleteOrder\Plugin
 */
class PluginAbstract
{
    /**
     * @var AclRetriever
     */
    protected $aclRetriever;
    /**
     * @var Session
     */
    protected $authSession;

    /**
     * PluginAbstract constructor.
     * @param AclRetriever $aclRetriever
     * @param Session $authSession
     */
    public function __construct(
        AclRetriever $aclRetriever,
        Session $authSession
    )
    {
        $this->aclRetriever = $aclRetriever;
        $this->authSession = $authSession;
    }

    /**
     * @return bool
     */
    public function isAllowedResources()
    {
        $user = $this->authSession->getUser();
        $role = $user->getRole();
        $resources = $this->aclRetriever->getAllowedResourcesByRole($role->getId());
        if (in_array("Magento_Backend::all", $resources) || in_array("Udeytech_DeleteOrder::delete_order", $resources)) {
            return true;
        }
        return false;
    }
}

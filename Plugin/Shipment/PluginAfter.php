<?php
/*
 * *
 *  * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */

namespace Udeytech\DeleteOrder\Plugin\Shipment;

use Magento\Authorization\Model\Acl\AclRetriever;
use Magento\Backend\Helper\Data;
use Magento\Backend\Model\Auth\Session;
use Magento\Shipping\Block\Adminhtml\View;
use Udeytech\DeleteOrder\Plugin\PluginAbstract;

/**
 * Class PluginAfter
 * @package Udeytech\DeleteOrder\Plugin\Shipment
 */
class PluginAfter extends PluginAbstract
{
    /**
     * @var Data
     */
    protected $data;

    /**
     * PluginAfter constructor.
     * @param AclRetriever $aclRetriever
     * @param Session $authSession
     * @param Data $data
     */
    public function __construct(
        AclRetriever $aclRetriever,
        Session $authSession,
        Data $data
    )
    {
        parent::__construct($aclRetriever, $authSession);
        $this->data = $data;
    }

    /**
     * @param View $subject
     * @param $result
     * @return mixed
     */
    public function afterGetBackUrl(View $subject, $result)
    {
        if ($this->isAllowedResources()) {
            $params = $subject->getRequest()->getParams();
            $message = __('Are you sure you want to do this?');
            if ($subject->getRequest()->getFullActionName() == 'adminhtml_order_shipment_view') {
                $subject->addButton(
                    'Udeytech-delete',
                    ['label' => __('Delete'), 'onclick' => 'confirmSetLocation(\'' . $message . '\',\'' . $this->getDeleteUrl($params['shipment_id']) . '\')', 'class' => 'Udeytech-delete'],
                    -1
                );
            }
        }
        return $result;
    }

    /**
     * @param $shipmentId
     * @return mixed
     */
    public function getDeleteUrl($shipmentId)
    {
        return $this->data->getUrl(
            'deleteorder/delete/shipment',
            [
                'shipment_id' => $shipmentId
            ]
        );
    }
}

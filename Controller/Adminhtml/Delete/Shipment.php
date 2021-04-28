<?php
/*
 * *
 *  * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */

namespace Udeytech\DeleteOrder\Controller\Adminhtml\Delete;

use Exception;
use Magento\Backend\App\Action;
use Udeytech\DeleteOrder\Model\Shipment\Delete;

/**
 * Class Shipment
 * @package Udeytech\DeleteOrder\Controller\Adminhtml\Delete
 */
class Shipment extends Action
{
    /**
     * @var \Magento\Sales\Model\Order\Shipment
     */
    protected $shipment;
    /**
     * @var Delete
     */
    protected $delete;

    /**
     * Shipment constructor.
     * @param Action\Context $context
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @param Delete $delete
     */
    public function __construct(
        Action\Context $context,
        \Magento\Sales\Model\Order\Shipment $shipment,
        Delete $delete
    )
    {
        $this->shipment = $shipment;
        $this->delete = $delete;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $shipment = $this->shipment->load($shipmentId);
        try {
            $this->delete->deleteShipment($shipmentId);
            $this->messageManager->addSuccessMessage(__('Successfully deleted shipment #%1.', $shipment->getIncrementId()));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Error delete shipment #%1.', $shipment->getIncrementId()));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/shipment/');
        return $resultRedirect;
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Udeytech_DeleteOrder::delete_order');
    }
}

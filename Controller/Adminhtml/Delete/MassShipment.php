<?php
/*
 * *
 *  * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */

namespace Udeytech\DeleteOrder\Controller\Adminhtml\Delete;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Udeytech\DeleteOrder\Model\Shipment\Delete;

/**
 * Class MassShipment
 * @package Udeytech\DeleteOrder\Controller\Adminhtml\Delete
 */
class MassShipment extends AbstractMassAction
{
    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory
     */
    protected $shipmentCollectionFactory;
    /**
     * @var \Magento\Sales\Model\Order\Shipment
     */
    protected $shipment;
    /**
     * @var Delete
     */
    protected $delete;

    /**
     * MassShipment constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderManagementInterface $orderManagement
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @param Delete $delete
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderManagementInterface $orderManagement,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
        \Magento\Sales\Model\Order\Shipment $shipment,
        Delete $delete
    )
    {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->orderManagement = $orderManagement;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->shipment = $shipment;
        $this->delete = $delete;
    }

    /**
     * @param AbstractCollection $collection
     * @return mixed
     */
    protected function massAction(AbstractCollection $collection)
    {
        $params = $this->getRequest()->getParams();
        $selected = [];
        $collectionShipment = $this->filter->getCollection($this->shipmentCollectionFactory->create());
        foreach ($collectionShipment as $shipment) {
            array_push($selected, $shipment->getId());
        }
        if ($selected) {
            foreach ($selected as $shipmentId) {
                $shipment = $this->getShipmentbyId($shipmentId);
                try {
                    $order = $this->deleteShipment($shipmentId);
                    $this->messageManager->addSuccessMessage(__('Successfully deleted shipment #%1.', $shipment->getIncrementId()));
                } catch (Exception $e) {
                    $this->messageManager->addErrorMessage(__('Error delete shipment #%1.', $shipment->getIncrementId()));
                }
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/shipment/');
        if ($params['namespace'] == 'sales_order_view_shipment_grid') {
            $resultRedirect->setPath('sales/order/view', ['order_id' => $order->getId()]);
        } else {
            $resultRedirect->setPath('sales/shipment/');
        }
        return $resultRedirect;
    }

    /**
     * @param $shipmentId
     * @return mixed
     */
    protected function getShipmentbyId($shipmentId)
    {
        return $this->shipment->load($shipmentId);
    }

    /**
     * @param $shipmentId
     * @return mixed
     */
    protected function deleteShipment($shipmentId)
    {
        return $this->delete->deleteShipment($shipmentId);
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Udeytech_DeleteOrder::delete_order');
    }
}

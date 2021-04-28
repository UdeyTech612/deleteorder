<?php
/*
 * *
 *  * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */

namespace Udeytech\DeleteOrder\Model\Shipment;

use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;
use Udeytech\DeleteOrder\Helper\Data;

/**
 * Class Delete
 * @package Udeytech\DeleteOrder\Model\Shipment
 */
class Delete
{
    /**
     * @var ResourceConnection
     */
    protected $resource;
    /**
     * @var Data
     */
    protected $data;
    /**
     * @var Shipment
     */
    protected $shipment;
    /**
     * @var Order
     */
    protected $order;

    /**
     * Delete constructor.
     * @param ResourceConnection $resource
     * @param Data $data
     * @param Shipment $shipment
     * @param Order $order
     */
    public function __construct(
        ResourceConnection $resource,
        Data $data,
        Shipment $shipment,
        Order $order
    )
    {
        $this->resource = $resource;
        $this->data = $data;
        $this->shipment = $shipment;
        $this->order = $order;
    }

    /**
     * @param $shipmentId
     * @return mixed
     */
    public function deleteShipment($shipmentId)
    {
        $connection = $this->resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $shipmentTable = $connection->getTableName($this->data->getTableName('sales_shipment'));
        $shipmentGridTable = $connection->getTableName($this->data->getTableName('sales_shipment_grid'));
        $shipment = $this->shipment->load($shipmentId);
        $orderId = $shipment->getOrder()->getId();
        $order = $this->order->load($orderId);
        $orderItems = $order->getAllItems();
        $shipmentItems = $shipment->getAllItems();
        foreach ($orderItems as $item) {
            foreach ($shipmentItems as $shipmentItem) {
                if ($shipmentItem->getOrderItemId() == $item->getItemId()) {
                    $item->setQtyShipped($item->getQtyShipped() - $shipmentItem->getQty());
                }
            }
        }
        $connection->rawQuery('DELETE FROM `' . $shipmentGridTable . '` WHERE entity_id=' . $shipmentId);
        $connection->rawQuery('DELETE FROM `' . $shipmentTable . '` WHERE entity_id=' . $shipmentId);
        if ($order->hasShipments() || $order->hasInvoices() || $order->hasCreditmemos()) {
            $order->setState(Order::STATE_PROCESSING)
                ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING))
                ->save();
        } else {
            $order->setState(Order::STATE_NEW)
                ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_NEW))
                ->save();
        }
        return $order;
    }
}

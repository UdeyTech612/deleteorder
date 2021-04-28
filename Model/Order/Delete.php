<?php
/*
 * *
 *  * Copyright (c) 2021. Udeytech Technologies All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */

namespace Udeytech\DeleteOrder\Model\Order;

use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\Order;
use Udeytech\DeleteOrder\Helper\Data;

/**
 * Class Delete
 * @package Udeytech\DeleteOrder\Model\Order
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
     * @var Order
     */
    protected $order;

    /**
     * Delete constructor.
     * @param ResourceConnection $resource
     * @param Data $data
     * @param Order $order
     */
    public function __construct(
        ResourceConnection $resource,
        Data $data,
        Order $order
    )
    {
        $this->resource = $resource;
        $this->data = $data;
        $this->order = $order;
    }

    /**
     * @param $orderId
     */
    public function deleteOrder($orderId)
    {
        $connection = $this->resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $invoiceGridTable = $connection->getTableName($this->data->getTableName('sales_invoice_grid'));
        $shippmentGridTable = $connection->getTableName($this->data->getTableName('sales_shipment_grid'));
        $creditmemoGridTable = $connection->getTableName($this->data->getTableName('sales_creditmemo_grid'));
        $order = $this->order->load($orderId);
        $order->delete();
        $connection->rawQuery('DELETE FROM `' . $invoiceGridTable . '` WHERE order_id=' . $orderId);
        $connection->rawQuery('DELETE FROM `' . $shippmentGridTable . '` WHERE order_id=' . $orderId);
        $connection->rawQuery('DELETE FROM `' . $creditmemoGridTable . '` WHERE order_id=' . $orderId);
    }
}

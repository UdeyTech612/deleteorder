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
use Udeytech\DeleteOrder\Model\Order\Delete;

/**
 * Class Order
 * @package Udeytech\DeleteOrder\Controller\Adminhtml\Delete
 */
class Order extends Action
{

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;
    /**
     * @var Delete
     */
    protected $delete;

    /**
     * Order constructor.
     * @param Action\Context $context
     * @param \Magento\Sales\Model\Order $order
     * @param Delete $delete
     */
    public function __construct(
        Action\Context $context,
        \Magento\Sales\Model\Order $order,
        Delete $delete
    )
    {
        $this->order = $order;
        $this->delete = $delete;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->order->load($orderId);
        $incrementId = $order->getIncrementId();
        try {
            $this->delete->deleteOrder($orderId);
            $this->messageManager->addSuccessMessage(__('Successfully deleted order #%1.', $incrementId));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Error delete order #%1.', $incrementId));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/order/');
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

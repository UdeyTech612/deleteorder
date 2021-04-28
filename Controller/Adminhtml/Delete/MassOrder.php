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
use Udeytech\DeleteOrder\Model\Order\Delete;

/**
 * Class MassOrder
 * @package Udeytech\DeleteOrder\Controller\Adminhtml\Delete
 */
class MassOrder extends AbstractMassAction
{

    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;
    /**
     * @var CollectionFactory
     */
    protected $orderCollectionFactory;
    /**
     * @var Delete
     */
    protected $delete;

    /**
     * MassOrder constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderManagementInterface $orderManagement
     * @param CollectionFactory $orderCollectionFactory
     * @param Delete $delete
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderManagementInterface $orderManagement,
        CollectionFactory $orderCollectionFactory,
        Delete $delete
    )
    {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->orderManagement = $orderManagement;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->delete = $delete;
    }

    /**
     * @param AbstractCollection $collection
     * @return mixed
     */
    protected function massAction(AbstractCollection $collection)
    {
        $collectionInvoice = $this->filter->getCollection($this->orderCollectionFactory->create());

        foreach ($collectionInvoice as $order) {
            $orderId = $order->getId();
            $incrementId = $order->getIncrementId();
            try {
                $this->deleteOrder($orderId);
                $this->messageManager->addSuccessMessage(__('Successfully deleted order #%1.', $incrementId));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Error delete order #%1.', $incrementId));
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/order/');
        return $resultRedirect;
    }

    /**
     * @param $orderId
     */
    protected function deleteOrder($orderId)
    {
        $this->delete->deleteOrder($orderId);
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Udeytech_DeleteOrder::delete_order');
    }
}

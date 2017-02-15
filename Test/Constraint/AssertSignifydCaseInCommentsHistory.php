<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Signifyd\Test\Constraint;

use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\Sales\Test\Page\Adminhtml\SalesOrderView;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that comment about authorized amount exists in Comments History section on order page in Admin.
 */
class AssertSignifydCaseInCommentsHistory extends AbstractConstraint
{
    /**
     * Pattern of message about authorized amount in order.
     */
    const CASE_CREATED_PATTERN = '/Signifyd Case (\d)+ has been created for order\./';

    /**
     * Assert that comment about authorized amount exists in Comments History section on order page in Admin.
     *
     * @param SalesOrderView $salesOrderView
     * @param OrderIndex $salesOrder
     * @param string $orderId
     * @param array $prices
     * @return void
     */
    public function processAssert(
        SalesOrderView $salesOrderView,
        OrderIndex $salesOrder,
        $orderId,
        array $prices
    ) {
        $salesOrder->open();
        $salesOrder->getSalesOrderGrid()->searchAndOpen(['id' => $orderId]);

        /** @var \Magento\Sales\Test\Block\Adminhtml\Order\View\Tab\Info $infoTab */
        $infoTab = $salesOrderView->getOrderForm()->openTab('info')->getTab('info');
        $latestComment = $infoTab->getCommentsHistoryBlock()->getLatestComment();

        \PHPUnit_Framework_Assert::assertRegExp(
            sprintf(self::CASE_CREATED_PATTERN, $prices['grandTotal']),
            $latestComment['comment'],
            'Signifyd case is not created for the order #' . $orderId
        );
    }

    /**
     * Returns string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "Message about Signifyd Case is available in Comments History section.";
    }
}

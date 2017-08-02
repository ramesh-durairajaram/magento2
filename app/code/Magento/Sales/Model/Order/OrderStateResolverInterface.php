<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Model\Order;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface OrderStateResolverInterface
 *
 * @api
 * @since 2.2.0
 */
interface OrderStateResolverInterface
{
    const IN_PROGRESS = 'order_in_progress';
    const FORCED_CREDITMEMO = 'forced_creditmemo';

    /**
     * @param OrderInterface $order
     * @param array $arguments
     * @return string
     * @since 2.2.0
     */
    public function getStateForOrder(OrderInterface $order, array $arguments = []);
}

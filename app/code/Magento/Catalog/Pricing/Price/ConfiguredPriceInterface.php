<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Pricing\Price;

use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;

/**
 * Configured price interface
 * @since 2.0.0
 */
interface ConfiguredPriceInterface
{
    /**
     * Price type configured
     */
    const CONFIGURED_PRICE_CODE = 'configured_price';

    /**
     * @param ItemInterface $item
     * @return $this
     * @since 2.0.0
     */
    public function setItem(ItemInterface $item);
}

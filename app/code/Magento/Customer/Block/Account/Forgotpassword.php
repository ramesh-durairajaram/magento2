<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Block\Account;

use Magento\Customer\Model\Url;
use Magento\Framework\View\Element\Template;

/**
 * Customer account navigation sidebar
 *
 * @api
 * @since 2.0.0
 */
class Forgotpassword extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Url
     * @since 2.0.0
     */
    protected $customerUrl;

    /**
     * @param Template\Context $context
     * @param Url $customerUrl
     * @param array $data
     * @since 2.0.0
     */
    public function __construct(
        Template\Context $context,
        Url $customerUrl,
        array $data = []
    ) {
        $this->customerUrl = $customerUrl;
        parent::__construct($context, $data);
    }

    /**
     * Get login URL
     *
     * @return string
     * @since 2.0.0
     */
    public function getLoginUrl()
    {
        return $this->customerUrl->getLoginUrl();
    }
}

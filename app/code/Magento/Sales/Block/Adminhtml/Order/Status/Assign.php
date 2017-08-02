<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Block\Adminhtml\Order\Status;

/**
 * @api
 * @since 2.0.0
 */
class Assign extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Constructor
     *
     * @return void
     * @since 2.0.0
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_order_status';
        $this->_mode = 'assign';
        $this->_blockGroup = 'Magento_Sales';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Status Assignment'));
        $this->buttonList->remove('delete');
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     * @since 2.0.0
     */
    public function getHeaderText()
    {
        return __('Assign Order Status to State');
    }
}

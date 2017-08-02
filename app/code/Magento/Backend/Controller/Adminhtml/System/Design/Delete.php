<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Controller\Adminhtml\System\Design;

/**
 * Class \Magento\Backend\Controller\Adminhtml\System\Design\Delete
 *
 * @since 2.0.0
 */
class Delete extends \Magento\Backend\Controller\Adminhtml\System\Design
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @since 2.0.0
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $design = $this->_objectManager->create(\Magento\Framework\App\DesignInterface::class)->load($id);

            try {
                $design->delete();
                $this->messageManager->addSuccess(__('You deleted the design change.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __("You can't delete the design change."));
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('adminhtml/*/');
    }
}

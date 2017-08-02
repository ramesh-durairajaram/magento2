<?php
namespace Magento\GoogleOptimizer\Model;

/**
 * Google Experiment Code Model
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @method \Magento\GoogleOptimizer\Model\ResourceModel\Code _getResource()
 * @method \Magento\GoogleOptimizer\Model\ResourceModel\Code getResource()
 * @method \Magento\GoogleOptimizer\Model\Code setEntityId(int $value)
 * @method string getEntityId()
 * @method \Magento\GoogleOptimizer\Model\Code setEntityType(string $value)
 * @method string getEntityType()
 * @method \Magento\GoogleOptimizer\Model\Code setStoreId(int $value)
 * @method int getStoreId()
 * @method \Magento\GoogleOptimizer\Model\Code setExperimentScript(int $value)
 * @method string getExperimentScript()
 * @api
 * @since 2.0.0
 */
class Code extends \Magento\Framework\Model\AbstractModel
{
    /**#@+
     * Entity types
     */
    const ENTITY_TYPE_PRODUCT = 'product';

    const ENTITY_TYPE_CATEGORY = 'category';

    const ENTITY_TYPE_PAGE = 'cms';

    /**#@-*/

    /**
     * @var bool
     * @since 2.0.0
     */
    protected $_validateEntryFlag = false;

    /**
     * Model construct that should be used for object initialization
     * @return void
     * @since 2.0.0
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Magento\GoogleOptimizer\Model\ResourceModel\Code::class);
    }

    /**
     * Loading by entity id and type type
     *
     * @param int $entityId
     * @param string $entityType One of self::CODE_ENTITY_TYPE_
     * @param int $storeId
     * @return $this
     * @since 2.0.0
     */
    public function loadByEntityIdAndType($entityId, $entityType, $storeId = 0)
    {
        $this->getResource()->loadByEntityType($this, $entityId, $entityType, $storeId);
        $this->_afterLoad();
        return $this;
    }
}

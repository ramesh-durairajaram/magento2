<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Api;

/**
 * @api
 * @since 2.0.0
 */
interface AttributeSetRepositoryInterface
{
    /**
     * Save attribute set data
     *
     * @param \Magento\Eav\Api\Data\AttributeSetInterface $attributeSet
     * @return \Magento\Eav\Api\Data\AttributeSetInterface saved attribute set
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException If attribute set is not found
     * @since 2.0.0
     */
    public function save(\Magento\Eav\Api\Data\AttributeSetInterface $attributeSet);

    /**
     * Retrieve list of Attribute Sets
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Eav\Api\Data\AttributeSetSearchResultsInterface
     * @since 2.0.0
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve attribute set information based on given ID
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $attributeSetId is not found
     * @return \Magento\Eav\Api\Data\AttributeSetInterface
     * @since 2.0.0
     */
    public function get($attributeSetId);

    /**
     * Remove given attribute set
     *
     * @param \Magento\Eav\Api\Data\AttributeSetInterface $attributeSet
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @return bool
     * @since 2.0.0
     */
    public function delete(\Magento\Eav\Api\Data\AttributeSetInterface $attributeSet);

    /**
     * Remove attribute set by given ID
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @return bool
     * @since 2.0.0
     */
    public function deleteById($attributeSetId);
}

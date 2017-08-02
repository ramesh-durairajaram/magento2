<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Ui\Api;

/**
 * Bookmark CRUD interface
 *
 * @api
 * @since 2.0.0
 */
interface BookmarkRepositoryInterface
{
    /**
     * Save bookmark
     *
     * @param \Magento\Ui\Api\Data\BookmarkInterface $bookmark
     * @return \Magento\Ui\Api\Data\BookmarkInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @since 2.0.0
     */
    public function save(\Magento\Ui\Api\Data\BookmarkInterface $bookmark);

    /**
     * Retrieve bookmark
     *
     * @param int $bookmarkId
     * @return \Magento\Ui\Api\Data\BookmarkInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @since 2.0.0
     */
    public function getById($bookmarkId);

    /**
     * Retrieve bookmarks matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Ui\Api\Data\BookmarkSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @since 2.0.0
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete bookmark
     *
     * @param \Magento\Ui\Api\Data\BookmarkInterface $bookmark
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     * @since 2.0.0
     */
    public function delete(\Magento\Ui\Api\Data\BookmarkInterface $bookmark);

    /**
     * Delete bookmark by ID
     *
     * @param int $bookmarkId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @since 2.0.0
     */
    public function deleteById($bookmarkId);
}

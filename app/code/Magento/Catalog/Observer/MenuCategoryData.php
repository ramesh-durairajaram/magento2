<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Observer;

/**
 * Class \Magento\Catalog\Observer\MenuCategoryData
 *
 * @since 2.0.0
 */
class MenuCategoryData
{
    /**
     * Catalog category
     *
     * @var \Magento\Catalog\Helper\Category
     * @since 2.0.0
     */
    protected $catalogCategory;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     * @since 2.0.0
     */
    private $catalogLayer = null;

    /**
     * Catalog layer resolver
     *
     * @var \Magento\Catalog\Model\Layer\Resolver
     * @since 2.0.0
     */
    protected $layerResolver;

    /**
     * @var \Magento\Framework\Registry
     * @since 2.0.0
     */
    protected $registry;

    /**
     * @param \Magento\Catalog\Helper\Category $catalogCategory
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\Registry $registry
     * @since 2.0.0
     */
    public function __construct(
        \Magento\Catalog\Helper\Category $catalogCategory,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Registry $registry
    ) {
        $this->catalogCategory = $catalogCategory;
        $this->layerResolver = $layerResolver;
        $this->registry = $registry;
    }

    /**
     * Get category data to be added to the Menu
     *
     * @param \Magento\Framework\Data\Tree\Node $category
     * @return array
     * @since 2.0.0
     */
    public function getMenuCategoryData($category)
    {
        $nodeId = 'category-node-' . $category->getId();

        $isActiveCategory = false;
        /** @var \Magento\Catalog\Model\Category $currentCategory */
        $currentCategory = $this->registry->registry('current_category');
        if ($currentCategory && $currentCategory->getId() == $category->getId()) {
            $isActiveCategory = true;
        }

        $categoryData = [
            'name' => $category->getName(),
            'id' => $nodeId,
            'url' => $this->catalogCategory->getCategoryUrl($category),
            'has_active' => $this->hasActive($category),
            'is_active' => $isActiveCategory,
        ];

        return $categoryData;
    }

    /**
     * Checks whether category belongs to active category's path
     *
     * @param \Magento\Framework\Data\Tree\Node $category
     * @return bool
     * @since 2.0.0
     */
    protected function hasActive($category)
    {
        $catalogLayer = $this->getCatalogLayer();
        if (!$catalogLayer) {
            return false;
        }

        $currentCategory = $catalogLayer->getCurrentCategory();
        if (!$currentCategory) {
            return false;
        }

        $categoryPathIds = explode(',', $currentCategory->getPathInStore());
        return in_array($category->getId(), $categoryPathIds);
    }

    /**
     * Get catalog layer
     * @return \Magento\Catalog\Model\Layer
     * @since 2.0.0
     */
    private function getCatalogLayer()
    {
        if ($this->catalogLayer === null) {
            $this->catalogLayer = $this->layerResolver->get();
        }
        return $this->catalogLayer;
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Block\Widget\Grid;

/**
 * @api
 * @deprecated 2.2.0 in favour of UI component implementation
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 2.0.0
 */
class ColumnSet extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Backend\Model\Widget\Grid\Row\UrlGenerator
     * @since 2.0.0
     */
    protected $_rowUrlGenerator;

    /**
     * Column headers visibility
     *
     * @var boolean
     * @since 2.0.0
     */
    protected $_headersVisibility = true;

    /**
     * Filter visibility
     *
     * @var boolean
     * @since 2.0.0
     */
    protected $_filterVisibility = true;

    /**
     * Empty grid text
     *
     * @var string|null
     * @since 2.0.0
     */
    protected $_emptyText;

    /**
     * Empty grid text CSS class
     *
     * @var string
     * @since 2.0.0
     */
    protected $_emptyTextCss = 'empty-text';

    /**
     * Label for empty cell
     *
     * @var string
     * @since 2.0.0
     */
    protected $_emptyCellLabel = '';

    /**
     * Count subtotals
     *
     * @var boolean
     * @since 2.0.0
     */
    protected $_countSubTotals = false;

    /**
     * Count totals
     *
     * @var boolean
     * @since 2.0.0
     */
    protected $_countTotals = false;

    /**
     * Columns to group by
     *
     * @var string[]
     * @since 2.0.0
     */
    protected $_groupedColumn = [];

    /**
     * @var boolean
     * @since 2.0.0
     */
    protected $_isCollapsed;

    /**
     * Path to template file in theme
     *
     * @var string
     * @since 2.0.0
     */
    protected $_template = 'Magento_Backend::widget/grid/column_set.phtml';

    /**
     * @var \Magento\Backend\Model\Widget\Grid\SubTotals
     * @since 2.0.0
     */
    protected $_subTotals = null;

    /**
     * @var \Magento\Backend\Model\Widget\Grid\Totals
     * @since 2.0.0
     */
    protected $_totals = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Backend\Model\Widget\Grid\Row\UrlGeneratorFactory $generatorFactory
     * @param \Magento\Backend\Model\Widget\Grid\SubTotals $subtotals
     * @param \Magento\Backend\Model\Widget\Grid\Totals $totals
     * @param array $data
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @since 2.0.0
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Backend\Model\Widget\Grid\Row\UrlGeneratorFactory $generatorFactory,
        \Magento\Backend\Model\Widget\Grid\SubTotals $subtotals,
        \Magento\Backend\Model\Widget\Grid\Totals $totals,
        array $data = []
    ) {
        $generatorClassName = \Magento\Backend\Model\Widget\Grid\Row\UrlGenerator::class;
        if (isset($data['rowUrl'])) {
            $rowUrlParams = $data['rowUrl'];
            if (isset($rowUrlParams['generatorClass'])) {
                $generatorClassName = $rowUrlParams['generatorClass'];
            }
            $this->_rowUrlGenerator = $generatorFactory->createUrlGenerator(
                $generatorClassName,
                ['args' => $rowUrlParams]
            );
        }

        $this->setFilterVisibility(
            array_key_exists('filter_visibility', $data) ? (bool)$data['filter_visibility'] : true
        );

        parent::__construct($context, $data);

        $this->setEmptyText(isset($data['empty_text']) ? $data['empty_text'] : __('We couldn\'t find any records.'));

        $this->setEmptyCellLabel(
            isset($data['empty_cell_label']) ? $data['empty_cell_label'] : __('We couldn\'t find any records.')
        );

        $this->setCountSubTotals(isset($data['count_subtotals']) ? (bool)$data['count_subtotals'] : false);
        $this->_subTotals = $subtotals;

        $this->setCountTotals(isset($data['count_totals']) ? (bool)$data['count_totals'] : false);
        $this->_totals = $totals;
    }

    /**
     * Retrieve the list of columns
     *
     * @return array
     * @since 2.0.0
     */
    public function getColumns()
    {
        $columns = $this->getLayout()->getChildBlocks($this->getNameInLayout());
        foreach ($columns as $key => $column) {
            if (!$column->isDisplayed()) {
                unset($columns[$key]);
            }
        }
        return $columns;
    }

    /**
     * Count columns
     *
     * @return int
     * @since 2.0.0
     */
    public function getColumnCount()
    {
        return count($this->getColumns());
    }

    /**
     * Set sortability flag for columns
     *
     * @param bool $value
     * @return $this
     * @since 2.0.0
     */
    public function setSortable($value)
    {
        if ($value === false) {
            foreach ($this->getColumns() as $column) {
                $column->setSortable(false);
            }
        }
        return $this;
    }

    /**
     * Set custom renderer type for columns
     *
     * @param string $type
     * @param string $className
     * @return $this
     * @since 2.0.0
     */
    public function setRendererType($type, $className)
    {
        foreach ($this->getColumns() as $column) {
            $column->setRendererType($type, $className);
        }
        return $this;
    }

    /**
     * Set custom filter type for columns
     *
     * @param string $type
     * @param string $className
     * @return $this
     * @since 2.0.0
     */
    public function setFilterType($type, $className)
    {
        foreach ($this->getColumns() as $column) {
            $column->setFilterType($type, $className);
        }
        return $this;
    }

    /**
     * Prepare block for rendering
     *
     * @return void
     * @since 2.0.0
     */
    protected function _beforeToHtml()
    {
        $columns = $this->getColumns();
        foreach ($columns as $columnId => $column) {
            $column->setId($columnId);
            $column->setGrid($this->getGrid());
            if ($column->isGrouped()) {
                $this->isColumnGrouped($column->getIndex(), true);
            }
        }
        $last = array_pop($columns);
        if ($last) {
            $last->addHeaderCssClass('last');
        }
    }

    /**
     * Return row url for js event handlers
     *
     * @param \Magento\Framework\DataObject $item
     * @return string
     * @since 2.0.0
     */
    public function getRowUrl($item)
    {
        $url = '#';
        if (null !== $this->_rowUrlGenerator) {
            $url = $this->_rowUrlGenerator->getUrl($item);
        }
        return $url;
    }

    /**
     * Get children of specified item
     *
     * @param \Magento\Framework\DataObject $item
     * @return array
     * @since 2.0.0
     */
    public function getMultipleRows($item)
    {
        return $item->getChildren();
    }

    /**
     * Has children of specified item
     *
     * @param \Magento\Framework\DataObject $item
     * @return bool
     * @since 2.0.0
     */
    public function hasMultipleRows($item)
    {
        return $item->hasChildren() && count($item->getChildren()) > 0;
    }

    /**
     * Retrieve columns for multiple rows
     * @return array
     * @since 2.0.0
     */
    public function getMultipleRowColumns()
    {
        $columns = $this->getColumns();
        foreach ($this->_groupedColumn as $column) {
            unset($columns[$column]);
        }
        return $columns;
    }

    /**
     * Check whether subtotal should be rendered
     *
     * @param \Magento\Framework\DataObject $item
     * @return boolean
     * @since 2.0.0
     */
    public function shouldRenderSubTotal($item)
    {
        return $this->getCountSubTotals() && count($this->getMultipleRows($item)) > 0;
    }

    /**
     * Check whether total should be rendered
     *
     * @return boolean
     * @since 2.0.0
     */
    public function shouldRenderTotal()
    {
        return $this->getCountTotals() && count($this->getCollection()) > 0;
    }

    /**
     * Retrieve rowspan number
     *
     * @param \Magento\Framework\DataObject $item
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return int|false
     * @since 2.0.0
     */
    public function getRowspan($item, $column)
    {
        if ($this->isColumnGrouped($column)) {
            return count(
                $this->getMultipleRows($item)
            ) + count(
                $this->_groupedColumn
            ) - 1 + (int)$this->shouldRenderSubTotal(
                $item
            );
        }
        return false;
    }

    /**
     * Check whether given column is grouped
     *
     * @param string|object $column
     * @param string $value
     * @return bool|$this
     * @since 2.0.0
     */
    public function isColumnGrouped($column, $value = null)
    {
        if (null === $value) {
            if (is_object($column)) {
                return in_array($column->getIndex(), $this->_groupedColumn);
            }
            return in_array($column, $this->_groupedColumn);
        }
        $this->_groupedColumn[] = $column;
        return $this;
    }

    /**
     * Check whether should render empty cell
     *
     * @param \Magento\Framework\DataObject $item
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return boolean
     * @since 2.0.0
     */
    public function shouldRenderEmptyCell($item, $column)
    {
        return $item->getIsEmpty() && in_array($column['index'], $this->_groupedColumn);
    }

    /**
     * Retrieve colspan for empty cell
     *
     * @return int
     * @since 2.0.0
     */
    public function getEmptyCellColspan()
    {
        return $this->getColumnCount() - count($this->_groupedColumn);
    }

    /**
     * Check whether should render cell
     *
     * @param \Magento\Framework\DataObject $item
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return boolean
     * @since 2.0.0
     */
    public function shouldRenderCell($item, $column)
    {
        if ($this->isColumnGrouped($column) && $item->getIsEmpty()) {
            return true;
        }
        if (!$item->getIsEmpty()) {
            return true;
        }
        return false;
    }

    /**
     * Set visibility of column headers
     *
     * @param boolean $visible
     * @return void
     * @since 2.0.0
     */
    public function setHeadersVisibility($visible = true)
    {
        $this->_headersVisibility = $visible;
    }

    /**
     * Return visibility of column headers
     *
     * @return boolean
     * @since 2.0.0
     */
    public function isHeaderVisible()
    {
        return $this->_headersVisibility;
    }

    /**
     * Set visibility of filter
     *
     * @param bool $visible
     * @return void
     * @since 2.0.0
     */
    public function setFilterVisibility($visible = true)
    {
        $this->_filterVisibility = $visible;
    }

    /**
     * Return visibility of filter
     *
     * @return boolean
     * @since 2.0.0
     */
    public function isFilterVisible()
    {
        return $this->_filterVisibility;
    }

    /**
     * Set empty text CSS class
     *
     * @param string $cssClass
     * @return $this
     * @since 2.0.0
     */
    public function setEmptyTextClass($cssClass)
    {
        $this->_emptyTextCss = $cssClass;
        return $this;
    }

    /**
     * Return empty text CSS class
     *
     * @return string
     * @since 2.0.0
     */
    public function getEmptyTextClass()
    {
        return $this->_emptyTextCss;
    }

    /**
     * Retrieve label for empty cell
     *
     * @return string
     * @since 2.0.0
     */
    public function getEmptyCellLabel()
    {
        return $this->_emptyCellLabel;
    }

    /**
     * Set label for empty cell
     *
     * @param string $label
     * @return $this
     * @since 2.0.0
     */
    public function setEmptyCellLabel($label)
    {
        $this->_emptyCellLabel = $label;
        return $this;
    }

    /**
     * Set flag whether is collapsed
     *
     * @param bool $isCollapsed
     * @return $this
     * @since 2.0.0
     */
    public function setIsCollapsed($isCollapsed)
    {
        $this->_isCollapsed = $isCollapsed;
        return $this;
    }

    /**
     * Retrieve flag is collapsed
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     * @since 2.0.0
     */
    public function getIsCollapsed()
    {
        return $this->_isCollapsed;
    }

    /**
     * Return grid of current column set
     *
     * @return \Magento\Backend\Block\Widget\Grid
     * @since 2.0.0
     */
    public function getGrid()
    {
        return $this->getParentBlock();
    }

    /**
     * Return collection of current grid
     *
     * @return \Magento\Framework\Data\Collection
     * @since 2.0.0
     */
    public function getCollection()
    {
        return $this->getGrid()->getCollection();
    }

    /**
     * Set subtotals
     *
     * @param bool $flag
     * @return $this
     * @since 2.0.0
     */
    public function setCountSubTotals($flag = true)
    {
        $this->_countSubTotals = $flag;
        return $this;
    }

    /**
     * Return count subtotals
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     * @since 2.0.0
     */
    public function getCountSubTotals()
    {
        return $this->_countSubTotals;
    }

    /**
     * Set totals
     *
     * @param bool $flag
     * @return $this
     * @since 2.0.0
     */
    public function setCountTotals($flag = true)
    {
        $this->_countTotals = $flag;
        return $this;
    }

    /**
     * Return count totals
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     * @since 2.0.0
     */
    public function getCountTotals()
    {
        return $this->_countTotals;
    }

    /**
     * Retrieve subtotal for item
     *
     * @param \Magento\Framework\DataObject $item
     * @return \Magento\Framework\DataObject
     * @since 2.0.0
     */
    public function getSubTotals($item)
    {
        $this->_prepareSubTotals();
        $this->_subTotals->reset();
        return $this->_subTotals->countTotals($item->getChildren());
    }

    /**
     * Retrieve subtotal items
     *
     * @return \Magento\Framework\DataObject
     * @since 2.0.0
     */
    public function getTotals()
    {
        $this->_prepareTotals();
        $this->_totals->reset();
        return $this->_totals->countTotals($this->getCollection());
    }

    /**
     * Update item with first sub-item data
     *
     * @param \Magento\Framework\DataObject $item
     * @return void
     * @since 2.0.0
     */
    public function updateItemByFirstMultiRow(\Magento\Framework\DataObject $item)
    {
        $multiRows = $this->getMultipleRows($item);
        if (is_object($multiRows) && $multiRows instanceof \Magento\Framework\Data\Collection) {
            /** @var $multiRows \Magento\Framework\Data\Collection */
            $item->addData($multiRows->getFirstItem()->getData());
        } elseif (is_array($multiRows)) {
            $firstItem = $multiRows[0];
            $item->addData($firstItem);
        }
    }

    /**
     * Prepare sub-total object for counting sub-totals
     *
     * @return void
     * @since 2.0.0
     */
    public function _prepareSubTotals()
    {
        $columns = $this->_subTotals->getColumns();
        if (empty($columns)) {
            foreach ($this->getMultipleRowColumns() as $column) {
                if ($column->getTotal()) {
                    $this->_subTotals->setColumn($column->getIndex(), $column->getTotal());
                }
            }
        }
    }

    /**
     * Prepare total object for counting totals
     *
     * @return void
     * @since 2.0.0
     */
    public function _prepareTotals()
    {
        $columns = $this->_totals->getColumns();
        if (empty($columns)) {
            foreach ($this->getColumns() as $column) {
                if ($column->getTotal()) {
                    $this->_totals->setColumn($column->getIndex(), $column->getTotal());
                }
            }
        }
    }
}

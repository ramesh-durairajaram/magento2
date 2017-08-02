<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Indexer\Model\Message;

/**
 * Class \Magento\Indexer\Model\Message\Invalid
 *
 * @since 2.0.0
 */
class Invalid implements \Magento\Framework\Notification\MessageInterface
{
    /**
     * @var \Magento\Indexer\Model\Indexer\Collection
     * @since 2.0.0
     */
    protected $collection;

    /**
     * @var \Magento\Framework\UrlInterface
     * @since 2.0.0
     */
    protected $urlBuilder;

    /**
     * @param \Magento\Indexer\Model\Indexer\Collection $collection
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @since 2.0.0
     */
    public function __construct(
        \Magento\Indexer\Model\Indexer\Collection $collection,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->collection = $collection;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Check whether all indices are valid or not
     *
     * @return bool
     * @since 2.0.0
     */
    public function isDisplayed()
    {
        /** @var \Magento\Indexer\Model\Indexer $indexer */
        foreach ($this->collection->getItems() as $indexer) {
            if ($indexer->getStatus() == \Magento\Framework\Indexer\StateInterface::STATUS_INVALID) {
                return true;
            }
        }

        return false;
    }

    //@codeCoverageIgnoreStart

    /**
     * Retrieve unique message identity
     *
     * @return string
     * @since 2.0.0
     */
    public function getIdentity()
    {
        return md5('INDEX_INVALID');
    }

    /**
     * Retrieve message text
     *
     * @return \Magento\Framework\Phrase
     * @since 2.0.0
     */
    public function getText()
    {
        $url = $this->urlBuilder->getUrl('indexer/indexer/list');
        //@codingStandardsIgnoreStart
        return __(
            'One or more <a href="%1">indexers are invalid</a>. Make sure your <a href="%2" target="_blank">Magento cron job</a> is running.',
            $url,
            'http://devdocs.magento.com/guides/v2.0/config-guide/cli/config-cli-subcommands-cron.html#config-cli-cron-bkg'
        );
        //@codingStandardsIgnoreEnd
    }

    /**
     * Retrieve message severity
     *
     * @return int
     * @since 2.0.0
     */
    public function getSeverity()
    {
        return self::SEVERITY_MAJOR;
    }

    //@codeCoverageIgnoreEnd
}

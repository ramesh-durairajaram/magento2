<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Indexer\SaveHandler;

/**
 * Class \Magento\Framework\Indexer\SaveHandler\Batch
 *
 * @since 2.0.0
 */
class Batch
{
    /**
     * @param \Traversable $documents
     * @param int $size
     * @return \Generator
     * @since 2.0.0
     */
    public function getItems(\Traversable $documents, $size)
    {
        $i = 0;
        $batch = [];

        foreach ($documents as $documentName => $documentValue) {
            $batch[$documentName] = $documentValue;
            if (++$i == $size) {
                yield $batch;
                $i = 0;
                $batch = [];
            }
        }
        if (count($batch) > 0) {
            yield $batch;
        }
    }
}

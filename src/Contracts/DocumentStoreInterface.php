<?php

namespace Imamuseum\Harvester2\Contracts;

use Imamuseum\Harvester2\Contracts\SourceInterface;

/**
 * Interface Document Store Interface
 * @package
 */
interface DocumentStoreInterface
{
    public function getClient();
    public function compareIdsBySource(SourceInterface $source, $start = 0, $limit = 10000);
    public function deleteBySource(SourceInterface $source, $ids);
    public function indexOrUpdate($index, $type, $object, $id);
    public function createIndices($index = null);
    public function deleteIndices($index = null);
}

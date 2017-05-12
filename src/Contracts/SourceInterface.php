<?php

namespace Imamuseum\Harvester2\Contracts;

use Imamuseum\Harvester2\Contracts\TransformerInterface;

/**
 * Interface SourceInterface
 * @package
 */
interface SourceInterface
{
    public function getAllObjectIds($start = null, $take = null);
    public function queryObjects($offset, $since = null, $id = null);
    public function buildObjects($results);
    public function shouldUpdateAll();
    public function getConfig();
}

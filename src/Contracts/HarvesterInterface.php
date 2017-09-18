<?php

namespace Imamuseum\Harvester2\Contracts;

use Imamuseum\Harvester2\Contracts\SourceInterface;

/**
 * Interface HarvesterInterface
 * @package
 */
interface HarvesterInterface
{
    public function deleteOldObjects(SourceInterface $source = null, $id = null);
    public function updateObjects(SourceInterface $source = null, $id = null, $only_recent = true);
}

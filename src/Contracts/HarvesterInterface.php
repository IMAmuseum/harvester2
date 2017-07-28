<?php

namespace Imamuseum\Harvester2\Contracts;

/**
 * Interface HarvesterInterface
 * @package
 */
interface HarvesterInterface
{
    public function deleteOldObjects($source = null, $id = null);
    public function updateObjects($source = null, $id = null, $only_recent = true);
}

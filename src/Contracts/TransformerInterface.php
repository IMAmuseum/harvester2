<?php

namespace Imamuseum\Harvester2\Contracts;

/**
 * Interface TransformerInterface
 * @package
 */
interface TransformerInterface
{
    public function transform($query_results);
    public function getConfig();
}

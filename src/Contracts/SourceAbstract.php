<?php

namespace Imamuseum\Harvester2\Contracts;

use Imamuseum\Harvester2\Contracts\SourceInterface;
use Imamuseum\Harvester2\Contracts\TransformerInterface;


/**
 * This is a bit of a middle man
 */
abstract class SourceAbstract implements SourceInterface
{
    /**
     * @var config
     * query configuration
     * needs to include a query to fetch ids and updated_at columns
     * needs to include a query to fetch objects
     */
    protected $config;

    /**
     * @var transformer
     * Class to transform query results into objects
     */
    protected $transformer;

    /**
     * Constructor
     * @param $transformer   Requires a data transformer
     * @author Daniel Keller
     */
    public function __construct(TransformerInterface $transformer)
    {
        $this->config = $transformer->getConfig();
        $this->transformer = $transformer;
    }

    /**
     * There may be a situation where a source should
     * determine that all records should be updated
     *
     * e.g. A parsing schema has been changed
     * which requires all records to be updated
     *
     * e.g. An orbitrary rule that every 6 months
     * every record should be refreshed.
     * @author Daniel Keller
     */
    public function shouldUpdateAll()
    {
        return false;
    }

    /**
     * return source config
     *
     * @author Daniel Keller
     */
    public function getConfig()
    {
        return $this->config;
    }
}

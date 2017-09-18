<?php

namespace Imamuseum\Harvester2\Contracts;

use DB;
use Exception;
use Carbon\Carbon;
use Imamuseum\Harvester2\Contracts\SourceAbstract;
use Imamuseum\Harvester2\Contracts\ClientInterface;
use Imamuseum\Harvester2\Contracts\TransformerInterface;

/**
 * Queriable Client provides an interface for accessing sources
 * that lack public APIs but can be queried directly.
 * Relies on a config file to define id and object queries
 */
abstract class ApiSourceAbstract extends SourceAbstract
{
    /**
     * @var api_client
     * wrapper for api requests
     */
    protected $api_client;

    /**
     * Constructor
     * @param $api_client        Requires an api client
     * @param $transformer   Requires a data transformer
     * @author Daniel Keller
     */
    public function __construct($api_client, TransformerInterface $transformer)
    {
        parent::__construct($transformer);

        if (!$this->config['endpoint']) {
            throw new Exception("Failed to find endpoint in config settings for $config.");
        }

        if (!$api_client) {
            throw new Exception("No API client provided.");
        }

        $this->api_client = $api_client;
    }

    /**
     * Fetch all IDs from the given catalog - must be overriden
     * @param $start   Begin result pagination
     * @param $take    Number of results to return
     * @author Daniel Keller
     */
    public function getAllObjectIds($start=null, $take=null)
    {
        return [];
    }
}

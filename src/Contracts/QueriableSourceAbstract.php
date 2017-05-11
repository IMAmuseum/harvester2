<?php

namespace Imamuseum\Harvester2\Contracts;

use DB;
use Exception;
use Carbon\Carbon;
use Imamuseum\Harvester2\Contracts\SourceAbstract;
use Imamuseum\Harvester2\Contracts\TransformerInterface;

/**
 * Queriable Client provides an interface for accessing sources
 * that lack public APIs but can be queried directly.
 * Relies on a config file to define id and object queries
 */
abstract class QueriableSourceAbstract extends SourceAbstract
{
    /**
     * Constructor
     * @param $transformer   Requires a data transformer
     * @author Daniel Keller
     */
    public function __construct(TransformerInterface $transformer)
    {
        parent::__construct($transformer);

        if (!$this->config['database_connection']) {
            throw new Exception("Failed to find database_connection in config settings for $config.");
        }

        if (!$this->config['queries']['ids']) {
            throw new Exception("Failed to find queries.ids in config settings for $config.");
        }

        if (!$this->config['queries']['objects']) {
            throw new Exception("Failed to find queries.objects in config settings for $config.");
        }
    }

    /**
     * Fetch all IDs from the given catalog
     * @param $start   Begin result pagination
     * @param $take    Number of results to return
     * @author Daniel Keller
     */
    public function getAllObjectIds($start=null, $take=null)
    {
        return $this->getIds($start, $take);
    }

    /**
     * Fetch field ids from given catalog since given data
     * @param $start  Begin result pagination
     * @param $take   Number of results to return
     * @param $since  Date to fetch data since
     * @author Daniel Keller
     */
    protected function getIDs($start=null, $take=null, $since=null)
    {
        $query = DB::connection($this->config['database_connection'])
            ->table(
                DB::raw('(' . $this->config['queries']['ids'] . ')')
            )
            ->orderBy('id');

        if ($since) {
            $since = Carbon::now()->subDays($since)->toDateTimeString();
            $query = $query->where('updated_at', '>=', $since);
        }

        if ($take)  $query = $query->limit($take);
        if ($start) $query = $query->offset($start);

        return $query->pluck('id');
    }
}

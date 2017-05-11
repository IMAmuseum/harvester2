<?php

namespace Imamuseum\Harvester2\Sources;

use DB;
use Exception;
use Carbon\Carbon;
use Imamuseum\Harvester2\Contracts\QueriableSourceAbstract;

/**
 * Proficio Source provides an interface for accessing a proficio database
 */
class ProficioSource extends QueriableSourceAbstract
{
    /**
     * Query the objects based on config queries
     * @param $offset   Begin fetching result at this offset
     * @param $since    If provided only query records updated since
     * @param $id       If provided only query record of object_id
     * @author Daniel Keller
     */
    public function queryObjects($offset, $since = null, $id = null)
    {
        $limit = $this->config['limit'];

        // Fetch raw object data since latest change
        $q = DB::connection($this->config['database_connection'])
            ->table(
                DB::raw('(' . $this->config['queries']['objects'] . ')')
            )
            ->orderBy('object_id')
            ->offset($offset)
            ->limit($limit);

        // Apply since if given
        if ($since) {
            $since = Carbon::now()->subDays($since)->toDateTimeString();
            $q->where('updated_at', '>=', $since);
        }

        // Apply id if given
        if ($id) {
            $q->where('object_id', '=', $id);
        }

        return [
            'offset' => $offset += $limit,
            'raw' => $q->get(),
        ];
    }

    /**
     * Build the objects based on the parsing schemas
     * @param $results   Array of raw query results to be built into objects
     * @author Daniel Keller
     */
    public function buildObjects($results)
    {
        $objects = [];

        if (empty($results)) {
            return false;
        }

        // Build and format each object
        foreach ($results as $result) {
            $record = $this->transformer->transform($result, $this->config);
            $objects[] = $record;
        }

        return $objects;
    }
}

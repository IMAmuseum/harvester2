<?php

namespace Imamuseum\Harvester2\Contracts;

use Imamuseum\Harvester2\Contracts\HarvesterInterface;
use Imamuseum\Harvester2\Contracts\SourceInterface;
use Imamuseum\Harvester2\Contracts\DocumentStoreInterface;

abstract class HarvesterAbstract implements HarvesterInterface
{

    /**
     * @var sources
     */
    protected $sources;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(DocumentStoreInterface $store)
    {
        $this->store = $store;
        $this->sources = [];
        $this->config = config('harvester');
    }

    /**
     * Delete objects no longer found in source
     * @param $id  object id to delete if old
     * @author Daniel Keller
     */
    public function deleteOldObjects($source = null, $id = null)
    {
        // If source is provided delete from given source only
        $sources = $this->sources;
        if ($source) {
            if (!$this->sources[$source]) {
                throw new Exception("Source $source does not exist");
            }

            $sources = [];
            $sources[$source] = $this->sources[$source];
        }

        // delete from sources
        foreach ($sources as $name => $source) {
            dispatch(new \Imamuseum\Harvester2\Jobs\DeleteOldObjects($source, $this->store, $id, $name));
        }
    }

    /**
     * Insert/update objects from source to store
     * @param $source  Source of objects to insert/update
     * @param $id      object id to insert/update
     * @param $ignore_since  should all objects be updated insert/update
     * @author Daniel Keller
     */
    public function updateObjects($source = null, $id = null, $ignore_since = false)
    {
        // If source is provided update from given source only
        $sources = $this->sources;
        if ($source) {
            if (!$this->sources[$source]) {
                throw new Exception("Source $source does not exist");
            }

            $sources = [];
            $sources[$source] = $this->sources[$source];
        }

        // update from sources
        foreach ($sources as $name => $source) {
            // Get new object ids to insert
            $offset = 0;

            // If ignore_since is provided process ignore_since source results (ignore $since)
            $since = $this->config['since'];
            if (!$ignore_since || $source->shouldParseAll()) {
                $since = null;
            }

            while (true) {
                $results = $source->queryObjects($offset, $since, $id);
                $offset = $results['offset'];

                if (!$results['raw']) {
                    break;
                }

                dispatch(new \Imamuseum\Harvester2\Jobs\IndexUpdateObjects(
                    $source,
                    $this->store,
                    $results['raw'],
                    $name,
                    $offset
                ));
            }
        }
    }
}

<?php

namespace Imamuseum\Harvester2\Stores;

use Exception;
use Elasticsearch\ClientBuilder as Elasticsearch;
use Imamuseum\Harvester2\Contracts\DocumentStoreInterface;
use Imamuseum\Harvester2\Contracts\SourceInterface;

/**
 * Queriable Client provides an interface for accessing sources
 * that lack public APIs but can be queried directly.
 * Relies on a config file to define id and object queries
 */
class ElasticSearchStore implements DocumentStoreInterface
{
    protected $scroll_params;

    public function __construct()
    {
        $this->config = config('document_store');
        $this->scroll_params = [
            'scroll' => '10s',
            'sort' => '_doc',
            'size' => $this->config['size'],
            '_source' => ['_id'],
            'body' => [
                'query' => [
                    'match_all' => (object) []
                ]
            ]
        ];
    }


    /**
     * Returns the store client
     *
     * TODO: Not sure if this is a good idea.
     * Provides flexibitiy but could lead to poor practices
     *
     * @author Daniel Keller
     */
    public function getClient()
    {
        return $this->build();
    }


    /**
     * Builds and returns the store client
     *
     * Q: Why do we make this a separate function instead
     * of a instantiating one instance of elasticsearch
     * in the constructor?
     *
     * A: Laravel jobs are unable to serialize the elasticsearch
     * instance so it needs to be created before each use.
     *
     * @author Daniel Keller
     */
    private function build()
    {
        return Elasticsearch::create()->build();
    }


    /**
     * Returns all store ids not found in source
     * @param $source  source to compare
     * @author Daniel Keller
     */
    public function compareIdsBySource(SourceInterface $source, $start = 0, $limit = 10000)
    {
        $elasticsearch = $this->build();
        $elasticsearch_ids = [];
        $source_ids = [];

        // Get all source ids
        // Paginate to prevent hitting memory limit
        do {
            $tmp = $source->getAllObjectIds($start, $limit);
            $source_ids = array_merge($source_ids, $tmp);
            $start += $limit;
        } while (!empty($tmp));

        // Use elastic search scroll/scan to fetch all ids
        $this->scroll_params['index'] = $source->getConfig()['index'];
        $response = $elasticsearch->search($this->scroll_params);

        // Here we use elasticsearch's scroll feature to work through bulk data
        // Continue to process each request until there are no more
        while (count($response['hits']['hits']) > 0) {
            foreach ($response['hits']['hits'] as $doc) {
                $elasticsearch_ids[] = $doc['_id'];
            }

            $response = $elasticsearch->scroll([
                'scroll_id' => $response['_scroll_id'],
                'scroll' => '10s',
            ]);
        }

        // Get all ids that are in elasticsearch but not the source
        return array_diff($elasticsearch_ids, $source_ids);
    }


    /**
     * Delete records by Source ids and Source
     * @author Daniel Keller
     */
    public function deleteBySource(SourceInterface $source, $ids)
    {
        $elasticsearch = $this->build();

        foreach ($ids as $id) {
            $elasticsearch->delete([
                'index' => $source->getConfig()['index'],
                'type' => $source->getConfig()['type'],
                'id' => $id
            ]);
        }
    }


    /**
     * Index or Update an object in the given index
     * @author Daniel Keller
     */
    public function indexOrUpdate($index, $type, $id_property, $object)
    {
        $elasticsearch = $this->build();

        if (!isset($object[$id_property])) {
            throw new Exception('When inserting record into index: $index no property $id_property was found. '.var_dump($object));
        }

        $elasticsearch->update([
            'index' => $index,
            'type' => $type,
            'id' => $object[$id_property],
            'body' => [
                'doc' => $object,    // If exists replace the whole object
                'upsert' => $object  // If doesn't exist insert the whole object
            ]
        ]);
    }


    /**
     * Create Indices in document store
     * If index already exists ignore it
     * @author Daniel Keller
     */
    public function createIndices($index = null)
    {
        $elasticsearch = $this->build();

        $indices = $this->config['indices'];
        if ($index) {
            $indices = [$index];
        }

        foreach ($indices as $index) {
            $params = ['index' => $index];
            if ($elasticsearch->indices()->exists($params)) {
                continue;
            }

            // Create index using index specific settings and mappings
            if (config('document_store.mappings')) {
                $params['body']['mappings'] = config('document_store.mappings');
            }

            if (config('document_store.settings')) {
                $params['body']['settings'] = config('document_store.settings');
            }

            $elasticsearch->indices()->create($params);
        }

        return true;
    }


    /**
     * Delete Indices from document store
     * If index doesn't exists ignore it
     * @author Daniel Keller
     */
    public function deleteIndices($index = null)
    {
        $elasticsearch = $this->build();

        $indices = $this->config['indices'];
        if ($index) {
            $indices = [$index];
        }

        foreach ($indices as $index) {
            $params = ['index' => $index];
            if ($elasticsearch->indices()->exists($params)) {
                $elasticsearch->indices()->delete($params);
            }
        }

        return true;
    }
}

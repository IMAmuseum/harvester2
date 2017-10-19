<?php

namespace Imamuseum\Harvester2\Sources;

use Exception;
use Carbon\Carbon;
use Imamuseum\Harvester2\Clients\PictionClient;
use Imamuseum\Harvester2\Contracts\ApiSourceAbstract;
use Imamuseum\Harvester2\Contracts\TransformerInterface;

/**
 * Proficio Source provides an interface for accessing a proficio database
 */
class PictionSource extends ApiSourceAbstract
{
    /**
     * Constructor
     * @param $transformer   Requires a data transformer
     * @author Daniel Keller
     */
    public function __construct(TransformerInterface $transformer)
    {
        // Don't feel so good about this.
        // To instantiate the APIClient we need the source config
        // which isn't set until the SourceAbstract constructor
        // We pull the config from the Transformer now (which
        // is what the sourceAbstract does as well) but if
        // we decide to change that it will break here
        $api_client = new PictionClient($transformer->getConfig());
        parent::__construct($api_client, $transformer);
    }


    /**
     * Query the objects based on config queries
     * @param $offset   Begin fetching result at this offset
     * @param $since    If provided only query records updated since
     * @param $id       If provided only query record of id
     * @author Daniel Keller
     */
    public function queryObjects($offset, $since = null, $id = null)
    {
        $limit = $this->config['limit'];

        // Add params
        $params = isset($this->config['query_params']) ? $this->config['query_params'] : [];
        $params = array_merge($params, [
            'START' => $offset,
            'MAXROWS' => $limit,
        ]);

        // Apply since if given
        if ($since) {
            $since = Carbon::now()->subDays($since);
            $date = $since->format('d-M-Y');
            $params['SEARCH'] .= ' ANY_DATE_UPDATED:>"'.$date.'"';
        }

        // Apply id if given
        if ($id) {
            $params = $this->applyIdToSearch($params, $id);
        }

        // execute request
        // Create Piction Client
        $response = $this->api_client->request($params);

        return [
            'offset' => $offset += $limit,
            'raw' => $response->r,
        ];
    }


    /**
     * Apply id "where clause" to the api request
     * This WILL be different per Piction instance
     * Overide this function in a child class if need be.
     *
     * @param $params   Search parameters
     * @param $id       id of object record
     * @author Daniel Keller
     */
    protected function applyIdToSearch($params, $id)
    {
        $params['SEARCH'] .= ' META:"ID,'.$id.'"';
        return $params;
    }
}

<?php

namespace App\Harvesters;

use DB;
use Schema;
use Exception;
use Illuminate\Database\Schema\Blueprint;
use App\Harvesters\MyCustomTransformer;

use Imamuseum\Harvester2\Contracts\DocumentStoreInterface;
use Imamuseum\Harvester2\Contracts\HarvesterAbstract;
use Imamuseum\Harvester2\Sources\ProficioSource;

/**
 * Class Mariners Harvester
 */
class MarinersHarvester extends HarvesterAbstract
{
    use \Illuminate\Foundation\Bus\DispatchesJobs;
    use SqliteFunctions;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(DocumentStoreInterface $store)
    {
        parent::__construct($store);
        $this->sources['my_source_name'] = new ProficioSource(new MyCustomTransformer('my_source_name'));
    }
}

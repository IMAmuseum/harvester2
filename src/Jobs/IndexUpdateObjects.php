<?php

namespace Imamuseum\Harvester2\Jobs;

use DB;
use Exception;
use Imamuseum\Harvester2\Jobs\InitializeJob;
use Imamuseum\Harvester2\Contracts\SourceInterface;
use Imamuseum\Harvester2\Contracts\DocumentStoreInterface;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;


class IndexUpdateObjects extends InitializeJob implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $source;
    protected $store;
    protected $raw;
    protected $source_name;
    protected $offset;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        SourceInterface $source,
        DocumentStoreInterface $store,
        $raw,
        $source_name = 'Untitled',
        $offset = null
    ) {
        $this->source = $source;
        $this->store = $store;
        $this->raw = $raw;
        $this->source_name = $source_name;
        $this->offset = $offset;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        logger("Index/Updateing Objects from ".$this->source_name.": ".$this->offset."\r");
        $objects = $this->source->buildObjects($this->raw);

        // Insert object into store
        foreach ($objects as $object) {
            $this->store->indexOrUpdate(
                $this->source->getConfig()['index'],
                $this->source->getConfig()['type'],
                $this->source->getConfig()['id_property'],
                $object
            );
        }
    }
}

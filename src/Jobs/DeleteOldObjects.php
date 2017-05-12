<?php

namespace Imamuseum\Harvester2\Jobs;

use DB;
use Exception;
use App\Jobs\Job;
use Imamuseum\Harvester2\Contracts\SourceInterface;
use Imamuseum\Harvester2\Contracts\DocumentStoreInterface;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;


class DeleteOldObjects extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $source;
    protected $store;
    protected $id;
    protected $source_name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        SourceInterface $source,
        DocumentStoreInterface $store,
        $id = null,
        $source_name = 'Untitled'
    ) {
        $this->source = $source;
        $this->store = $store;
        $this->id = $id;
        $this->source_name = $source_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        logger("Delete Old Objects from".$this->source_name."...");

        if ($this->id) {
            $delete_ids = [$this->id];
        } else {
            $delete_ids = $this->store->compareIdsBySource($this->source);
        }

        // delete objects in the source
        $this->store->deleteBySource($this->source, $delete_ids);
    }
}

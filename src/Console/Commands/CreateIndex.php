<?php

namespace Imamuseum\Harvester2\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Imamuseum\Harvester2\Contracts\DocumentStoreInterface;

class CreateIndex extends Command
{
    use \Illuminate\Foundation\Bus\DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-index
                            {--index=null : cms from which to sync (if absent all sources will sync)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Indices in document store';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(DocumentStoreInterface $store)
    {
        $index = $this->option('index') == 'null' ? null : $this->option('index');
        $store->deleteIndices($index);
        $store->createIndices($index);
    }
}

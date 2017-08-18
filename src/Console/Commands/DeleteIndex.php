<?php

namespace Imamuseum\Harvester2\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Imamuseum\Harvester2\Contracts\DocumentStoreInterface;

class DeleteIndex extends Command
{
    use \Illuminate\Foundation\Bus\DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-index
                            {--index=null : index to delete (if absent all indexes will be deleted)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Indices in document store';

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
        if ($index) {
            $message = "You are about to delete document store index $index!";
        } else {
            $message = 'You are about to delete all document store indices!';
        }

        if ($this->confirm("$message Do you wish to continue?")) {
            $store->deleteIndices($index);
        }
    }
}

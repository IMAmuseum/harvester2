## Harvester Package

This is a large overhaul of the harvester. The focus of this project was to optimize the harvester to avoid extraneous querying and allow que-able processing.

### Composer Setup
```json
    "require": {
        "imamuseum/harvester2": ""
    },
```

### Service Provider
In `config/app.php` add to the autoloaded providers -
```php
Imamuseum\Harvester2\HarvesterServiceProvider::class,
```

Add ExampleHarvester to `app/Providers/AppServiceProvider.php` to implement the HarvesterInterface.
```php
    public function register()
    {
        $this->app->bind('Imamuseum\Harvester2\Contracts\HarvesterInterface',
            'Imamuseum\Harvester2\ExampleHarvester');
    }
```

Now you can publish the package -
```sh
php artisan vendor:publish

```

Push items off the queue
Harvester 2 pushes delete jobs onto a queue named "high" and update jobs onto the default queue.
This allows the user to define a high and low priority queue to insure delete jobs are run before update jobs.
```sh
php artisan queue:listen
php artisan queue:listen --queue=high,low - prioritize "high" queue then "low" queue (or "default" or whatever you name your other queues)
```

Harvester2 has the flexibility to work with any queue and any document store.
At the moment ElasticSearch is the only Document Store that has been implemented.
Personally I suggest using Redis as a queue as it plays well with Laravel.
If you intend to use sqlite as a queue be sure to use an instance separate from any of your sources (if any of your sources are sqlite databases. The lack of concurrency may cause jobs to fail.

### Artisan Commands
```sh
php artisan create-index --index=optional - Re-Creates document Store indices according to config. Option to specify which index.
php artisan delete-index --index=optional - Deletes document Store indices according to config. Option to specify which index.
php artisan harvest --source=optional --id=null --recent=false - Runs sync. Option to specify source, id, and whether to pull all or most recently changed data
```
Use the --help flag after any command to view the available options with a description.

### Supported Sources
* Piction
* Proficio
* Generic API source
* Generic Query source

### Supported Document Stores
* ElasticSearch 5.0

### License
The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

